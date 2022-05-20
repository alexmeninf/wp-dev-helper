<?php

if (!defined('ABSPATH'))
  exit; // Exit if accessed directly.


if (!class_exists('WPDH_PWA')) :

  class WPDH_PWA
  {

    // Caminho completo do arquivo.
    private $path_file = PLUGINPATH . 'assets/js/wpdh-offiline-pwa.js';

    // Caminho da url do arquivo.
    public $root_file = PLUGINROOT . '/assets/js/wpdh-offiline-pwa.js';

    function __construct()
    {
      add_action('wp_ajax_regenerate_cache_files', [$this, 'regenerate_cache_files']);
      add_action('wp_ajax_nopriv_regenerate_cache_files', [$this, 'regenerate_cache_files']);
    }

    // Iniciar classe
    public function init()
    {
      $this->wpdh_cache_scripts();
      $notice = new Notices();

      if (get_option('wpdh_pwa_offline_files') == 'yes') {
        add_action('wp_footer', array($this, 'init_scripts'));
      } else {
        $notice->set_Message(__('WPA Offline Mode has been disabled. Generate files in the Offline Mode tab', 'wpdevhelper'));
        $notice->show_Warning();
      }
    }

    // Scripts
    public function init_scripts()
    {
      wp_enqueue_script('wpdh-offline-pwa', $this->root_file, array(), '1.0', true);
    }

    public function regenerate_cache_files()
    {
      check_ajax_referer('regenerate_cache_files_nonce', 'nonce');

      update_option('wpdh_pwa_offline_files', 'yes');
      $this->wpdh_cache_scripts(true);
    }

    /**
     * wpdh_cache_scripts
     * 
     * Cria o cache do arquivo para o site.
     *
     * @return void
     */
    private function wpdh_cache_scripts($regenerate = false)
    {
      $notice = new Notices();

      $op_files = get_option('wpdh_pwa_offline_files');

      try {
        if ($op_files == false || $regenerate) {
          if (is_writable($this->path_file)) {
            require_once PLUGINPATH . 'includes/wpdh-simple_html_dom.php';

            // $url = get_home_url();
            $url            = 'https://revistasaudevida.com.br/';
            $website        = file_get_html($url);
            $offiline_files = "'./', ";

            foreach ($website->find('link[rel="stylesheet"]') as $stylesheet) {
              $offiline_files .= "'" . $stylesheet->href . "', ";
            }

            foreach ($website->find('script[src*="revistasaudevida.com.br"]') as $script) {
              $offiline_files .= "'" . $script->src . "', ";
            }

            $contents    = file_get_contents($this->path_file);
            $new_content = str_replace('PRECACHE_URLS = [];', 'PRECACHE_URLS = [' . $offiline_files . '];', $contents);

            // Escreve o novo conteúdo no arquivo
            file_put_contents($this->path_file, $new_content);

            // Define novo opção no banco de dados, para verificar se possui arquivos offline
            if ($op_files == false) {
              add_option('wpdh_pwa_offline_files', 'yes');
            }
            
            if (!$regenerate) {
              $notice->set_Message(__('PWA mode activated successfully!', 'wpdevhelper'));
              $notice->show_Notice();
            } else {
              printf(json_encode(array('message' => __('Theme script paths have been regenerated for offline mode.', 'wpdevhelper'))));
            }

          } else {
            $txt = sprintf(__('WPA offline mode failed. Path %s is not writable.', 'wpdevhelper'), $this->path_file);
            if (!$regenerate) {
              $notice->set_Message($txt);
              $notice->show_Error();
            } else {
              printf(json_encode(array('message' => $txt)));
            }
          }
        }
      } catch (Exception $e) {
        printf(json_encode(array('message' => $wpdb->last_error)));
      }

      if ($regenerate) die();
    }
  }

endif;

$pwa = new WPDH_PWA();
$pwa->init();
