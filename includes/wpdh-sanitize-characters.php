<?php

/**
 * Limpar nome de arquivo no upload
 * 
 * Sanitization test done with the filename:
 * ÄäÆæÀàÁáÂâÃãÅåªₐāĆćÇçÐđÈèÉéÊêËëₑƒğĞÌìÍíÎîÏïīıÑñⁿÒòÓóÔôÕõØøₒÖöŒœßŠšşŞ™ÙùÚúÛûÜüÝýÿŽž¢€‰№$℃°C℉°F⁰¹²³⁴⁵⁶⁷⁸⁹₀₁₂₃₄₅₆₇₈₉±×₊₌⁼⁻₋–—‑․‥…‧.png
 * @author toscho
 * @url    https://github.com/toscho/Germanix-WordPress-Plugin
 */
function devh_sanitize_filename( $filename )
{

  $filename = html_entity_decode( $filename, ENT_QUOTES, 'utf-8' );
  $filename = devh_translit( $filename );
  $filename = devh_lower_ascii( $filename );
  $filename = devh_remove_doubles( $filename );
  return $filename;
}

/**
 * Converte maiúsculas em minúsculas e remove o resto.
 * https://github.com/toscho/Germanix-WordPress-Plugin
 *
 * @uses   apply_filters( 'germanix_lower_ascii_regex' )
 * @param  string $str Input string
 * @return string
 */
function devh_lower_ascii( $str )
{
  $str   = strtolower( $str );
  $regex = array(
    'pattern'     => '~([^a-z\d_.-])~', 
    'replacement'  => ''
  );
  // Leave underscores, otherwise the taxonomy tag cloud in the
  // backend won’t work anymore.
  return preg_replace( $regex['pattern'], $regex['replacement'], $str );
}


/**
 * Reduz meta caracteres (=+.) repetidos para apenas um.
 * https://github.com/toscho/Germanix-WordPress-Plugin
 *
 * @param  string $str Input string
 * @return string
 */
function devh_remove_doubles( $str )
{
  $regex = array(
    'pattern'     => '~([=+.])\1+~', 
    'replacement' => "\1"
  );
  return preg_replace( $regex['pattern'], $regex['replacement'], $str );
}


/**
 * Substitui caracteres não-ASCII.
 * https://github.com/toscho/Germanix-WordPress-Plugin
 *
 * Modified version of Heiko Rabe’s code.
 *
 * @author Heiko Rabe http://code-styling.de
 * @link   http://www.code-styling.de/?p=574
 * @param  string $str
 * @return string
 */
function devh_translit( $str )
{
  $utf8 = array(
    'Ä'  => 'Ae'
    , 'ä' => 'ae'
    , 'Æ' => 'Ae'
    , 'æ' => 'ae'
    , 'À' => 'A'
    , 'à' => 'a'
    , 'Á' => 'A'
    , 'á' => 'a'
    , 'Â' => 'A'
    , 'â' => 'a'
    , 'Ã' => 'A'
    , 'ã' => 'a'
    , 'Å' => 'A'
    , 'å' => 'a'
    , 'ª' => 'a'
    , 'ₐ' => 'a'
    , 'ā' => 'a'
    , 'Ć' => 'C'
    , 'ć' => 'c'
    , 'Ç' => 'C'
    , 'ç' => 'c'
    , 'Ð' => 'D'
    , 'đ' => 'd'
    , 'È' => 'E'
    , 'è' => 'e'
    , 'É' => 'E'
    , 'é' => 'e'
    , 'Ê' => 'E'
    , 'ê' => 'e'
    , 'Ë' => 'E'
    , 'ë' => 'e'
    , 'ₑ' => 'e'
    , 'ƒ' => 'f'
    , 'ğ' => 'g'
    , 'Ğ' => 'G'
    , 'Ì' => 'I'
    , 'ì' => 'i'
    , 'Í' => 'I'
    , 'í' => 'i'
    , 'Î' => 'I'
    , 'î' => 'i'
    , 'Ï' => 'Ii'
    , 'ï' => 'ii'
    , 'ī' => 'i'
    , 'ı' => 'i'
    , 'I' => 'I' // turkish, correct?
    , 'Ñ' => 'N'
    , 'ñ' => 'n'
    , 'ⁿ' => 'n'
    , 'Ò' => 'O'
    , 'ò' => 'o'
    , 'Ó' => 'O'
    , 'ó' => 'o'
    , 'Ô' => 'O'
    , 'ô' => 'o'
    , 'Õ' => 'O'
    , 'õ' => 'o'
    , 'Ø' => 'O'
    , 'ø' => 'o'
    , 'ₒ' => 'o'
    , 'Ö' => 'Oe'
    , 'ö' => 'oe'
    , 'Œ' => 'Oe'
    , 'œ' => 'oe'
    , 'ß' => 'ss'
    , 'Š' => 'S'
    , 'š' => 's'
    , 'ş' => 's'
    , 'Ş' => 'S'
    , '™' => 'TM'
    , 'Ù' => 'U'
    , 'ù' => 'u'
    , 'Ú' => 'U'
    , 'ú' => 'u'
    , 'Û' => 'U'
    , 'û' => 'u'
    , 'Ü' => 'Ue'
    , 'ü' => 'ue'
    , 'Ý' => 'Y'
    , 'ý' => 'y'
    , 'ÿ' => 'y'
    , 'Ž' => 'Z'
    , 'ž' => 'z'
    , '&' => 'e'
    // misc
    , '¢' => 'Cent'
    , '€' => 'Euro'
    , '‰' => 'promille'
    , '№' => 'Nr'
    , '$' => 'Dollar'
    , '℃' => 'Grad Celsius'
    , '°C' => 'Grad Celsius'
    , '℉' => 'Grad Fahrenheit'
    , '°F' => 'Grad Fahrenheit'
    // Superscripts
    , '⁰' => '0'
    , '¹' => '1'
    , '²' => '2'
    , '³' => '3'
    , '⁴' => '4'
    , '⁵' => '5'
    , '⁶' => '6'
    , '⁷' => '7'
    , '⁸' => '8'
    , '⁹' => '9'
    // Subscripts
    , '₀' => '0'
    , '₁' => '1'
    , '₂' => '2'
    , '₃' => '3'
    , '₄' => '4'
    , '₅' => '5'
    , '₆' => '6'
    , '₇' => '7'
    , '₈' => '8'
    , '₉' => '9'
    // Operators, punctuation
    , '±' => 'plusminus'
    , '×' => 'x'
    , '₊' => 'plus'
    , '₌' => '-'
    , '⁼' => '='
    , '⁻' => '-' // sup minus
    , '₋' => '-' // sub minus
    , '–' => '-' // ndash
    , '—' => '-' // mdash
    , '‑' => '-' // non breaking hyphen
    , '․' => '-' // one dot leader
    , '‥' => '-'  // two dot leader
    , '…' => '-'  // ellipsis
    , '‧' => '-' // hyphenation point
    , ' ' => '-'   // nobreak space
    , ' ' => '-'   // normal space
  );

  $str = strtr( $str, $utf8 );
  return trim( $str, '-' );
}