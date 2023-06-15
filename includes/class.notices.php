<?php

class Notices
{
  private $_admin_notice = "admin_notices"; // name of action
  private $_message;

  // 1. get Message
  public function set_Message($message)
  {
    $this->_message = $message;
  }

  // 2. define add_action admin notices for method error
  public function show_Error()
  {
    add_action($this->_admin_notice, array($this, 'set_error_Message'));
  }

  // 2a. define add_action admin notices for method notice
  public function show_Notice()
  {
    add_action($this->_admin_notice, array($this, 'set_notice_Message'));
  }

  // 2b. define add_action admin notices for method notice
  public function show_Warning()
  {
    add_action($this->_admin_notice, array($this, 'set_warning_Message'));
  }

  // 3. show admin error based on get Message method
  public function set_error_Message()
  {
    echo '<div class="notice notice-error is-dismissable"><p>' . $this->_message . '</p> </div>';
  }
  // 3a. show admin notice based on get Message method
  public function set_notice_Message()
  {
    echo '<div class="notice notice-success is-dismissible"><p>' . $this->_message . '</p></div>';
  }
  // 3b. show admin warning based on get Message method
  public function set_warning_Message()
  {
    echo '<div class="notice notice-warning is-dismissible"><p>' . $this->_message . '</p></div>';
  }
}
