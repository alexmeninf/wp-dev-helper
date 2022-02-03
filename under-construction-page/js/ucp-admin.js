/*
 * UnderConstructionPage
 * Main backend JS
 * (c) WebFactory Ltd, 2015 - 2022
 */

jQuery(document).ready(function($) {
  var old_settings = $('#ucp_form *').not('.skip-save').serialize();
  var ad_name = '';

  // init tabs
  $('#ucp_tabs').tabs({
    activate: function(event, ui) {
        Cookies.set('ucp_tabs', $('#ucp_tabs').tabs('option', 'active'), { expires: 365 });
    },
    active: Cookies.get('ucp_tabs')
  }).show();

  // init 2nd level of tabs
  $('.ucp-tabs-2nd-level').each(function() {
    $(this).tabs({
      activate: function(event, ui) {
        Cookies.set($(this).attr('id'), $(this).tabs('option', 'active'), { expires: 365 });
      },
      active: Cookies.get($(this).attr('id'))
    });
  });

  // init select2
  $('#whitelisted_users').select2({ 'placeholder': ucp.whitelisted_users_placeholder});


  // autosize textareas
  $.each($('textarea[data-autoresize]'), function() {
    var offset = this.offsetHeight - this.clientHeight;

    var resizeTextarea = function(el) {
        $(el).css('height', 'auto').css('height', el.scrollHeight + offset + 2);
    };
    $(this).on('keyup input click', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
  });


  // dismiss survey forever
  $('.dismiss-survey').on('click', function(e) {
    $('#features-survey-dialog').dialog('close');

    $.post(ajaxurl, { survey: $(this).data('survey'),
                      _ajax_nonce: ucp.nonce_dismiss_survey,
                      action: 'ucp_dismiss_survey'
    });

    e.preventDefault();
    return false;
  });

  // activate theme via thumb and save
  $('.ucp-thumb .activate-theme').on('click', function(e) {
    e.preventDefault();

    theme_id = $(this).parents('.ucp-thumb').data('theme-id');
    $('#theme_id').val(theme_id);
    $('#ucp_tabs #submit').trigger('click');

    return false;
  });


  // init datepicker
  $('.datepicker').AnyTime_picker({ format: "%Y-%m-%d %H:%i", firstDOW: 1, earliest: new Date(), labelTitle: "Select the date &amp; time when construction mode will be disabled" } );


  // fix when opening datepicker
  $('.show-datepicker').on('click', function(e) {
    e.preventDefault();

    $(this).prevAll('input.datepicker').focus();

    return false;
  });


  $('#ga_tracking_id_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#ga_tracking_id_wrapper').show();
      } else {
        $('#ga_tracking_id_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#ga_tracking_id_wrapper').hide();
      } else {
        $('#ga_tracking_id_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);

  $('#end_date_toggle').on('change', function(e, is_triggered) {
    if ($(this).is(':checked')) {
      if (is_triggered) {
        $('#end_date_wrapper').show();
      } else {
        $('#end_date_wrapper').slideDown();
      }
    } else {
      if (is_triggered) {
        $('#end_date_wrapper').hide();
      } else {
        $('#end_date_wrapper').slideUp();
      }
    }
  }).triggerHandler('change', true);


  $('.settings_page_ucp .wrap').on('click', '.reset-settings', function(e) {
    if (!confirm('Are you sure you want to reset all UCP settings to their default values? There is NO undo.')) {
      e.preventDefault();
      return false;
    }

    return true;
  }); // reset-settings


  // warning if there are unsaved changes when previewing
  $('.settings_page_ucp .wrap').on('click', '#ucp_preview', function(e) {
    if ($('#ucp_form *').not('.skip-save').serialize() != old_settings) {
      if (!confirm('There are unsaved changes that will not be visible in the preview. Please save changes first.\nContinue?')) {
        e.preventDefault();
        return false;
      }
    }

    return true;
  });


  // check if there are invalid fields
  // assume they are social icons
  $('.settings_page_ucp .wrap').on('click', '#submit', function(e) {
    if ($('#ucp_form input:invalid').not('.skip-save').length) {
      $('#ucp_tabs').tabs('option', 'active', 2);
      $('#ucp_form input:invalid').first().focus();
      alert('Please correct the errors before saving.');

      return false;
    }

    return true;
  }); // form submit


  // show all social icons
  $('.settings_page_ucp .wrap').on('click', '#show-social-icons', function(e) {
    $(this).hide();
    $('#ucp-social-icons tr').removeClass('hidden');

    return false;
  });


  // helper for linking anchors in different tabs
  $('.settings_page_ucp').on('click', '.change_tab', function(e) {
    e.preventDefault();
    $('#ucp_tabs').tabs('option', 'active', $(this).data('tab'));

    // get the link anchor and scroll to it
    target = this.href.split('#')[1];
    if (target) {
      $.scrollTo('#' + target, 500, {offset: {top:-50, left:0}});
    }

    return false;
  });

  $('.settings_page_ucp').on('click', '.go-to-license-key', function(e) {
    $('#upsell-dialog').dialog('close');
    $('#ucp_tabs').tabs('option', 'active', 5);
  });
}); // on ready


function ucp_fix_dialog_close(event, ui) {
  jQuery('.ui-widget-overlay').bind('click', function(){
    jQuery('#' + event.target.id).dialog('close');
  });
} // ucp_fix_dialog_close
