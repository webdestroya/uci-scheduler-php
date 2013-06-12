//
// megaboxx inbox functions
// note: the megaboxx object is just a motley grab bag of poorly designed functions
//       that are loosely related to megaboxx. proudly endorsed by marcel.
// =======================================================================================
var megaboxx = function() {

}

megaboxx.prototype.detect_url = function(obj) {
  if (megaboxx.share_added == true) return; 
  var url = '';
  if (url = obj.value.match(/www\S*[\s|\)|\!]/i)) {
    url = "http://" + url[0];
  } else {
    var match = '';
    if (match = obj.value.match(/http:\/\/\S*[\s|\)|\!]/i)) {
      url = match[0];
    }
  }
  if (url) {
    url = url.replace(/[\s|\)|\!]/, "");
    megaboxx.add_attachment_stage(megaboxx.share_html_block);
    var sa = ge('attachment_stage_area');
    attachments.attach_link_url(sa, url);
    megaboxx.share_added = true;
  } 
  return false;
}

// This hook gets called when the user changes the "Select:" dropdown
megaboxx.prototype.select_dropdown_onchange = function(obj) {
  if (obj.value == '^_^') {
    return false;
  }
  var status = obj.value ? this['STATUS_'+obj.value.toUpperCase()] : this.STATUS_NONE;
  this.set_selection(status);
}
 
// Called when the "Mark as New" "Mark as Read" etc links are clicked
megaboxx.prototype.status_menu_onclick = function(obj, action, threads/* = null*/) {
  threads = threads ? threads : this.get_selected_threads();
  if (!threads.length || (typeof obj == 'object' && obj.parentNode.className.indexOf('disabled')!=-1)) {
    this.update_status_buttons();
    return false;
  }

  var reload_needed=false;

  var post={action:action, ids:threads, folder:megaboxx_data.folder, time: megaboxx_data.time};
  if (action == 'delete') {
    if (typeof obj == 'boolean' && obj) {
      var boxx = ge('megaboxx');
      for (var i in threads) {
        var row = document.getElementById('thread_'+threads[i]);
        row.parentNode.removeChild(row);
      }
      post.page = megaboxx_data.page;
      post.nav_base = megaboxx_data.nav_base;
      post.slice = boxx.getElementsByTagName('tr').length;
      var loading = ge('loading_boxx');

      // use this to show the "you currently don't have any messages" and reset other visual details
      if (!boxx.getElementsByTagName('tr').length) {
        reload_needed=true;
      }
      if (!loading) {
        var loading = document.createElement('div');
        loading.innerHTML = ['<table id="loading_boxx" class="', boxx.className, '"><tr><td></td></tr></table>'].join('');
        loading = loading.getElementsByTagName('table')[0];
        boxx.parentNode.insertBefore(loading, boxx.nextSibling);
      } else {
        loading.style.display = '';
      }
    } else {
      var dialog = new contextual_dialog();
      dialog.set_context(obj);
      dialog.show_choice('Are you sure you want to delete ' + (threads.length==1 ? 'this thread' : 'these threads') + '?',
                         'This will remove the associated messages.',
                         'Delete', function(){this[0].status_menu_onclick(true, action, threads);this[1].hide()}.bind([this, dialog]),
                         'Cancel', function(){generic_dialog.get_dialog(this).hide()});
      return false;
    }
  } else {
    for (var i in threads) {
      var row = document.getElementById('thread_'+threads[i]);
      if (action == 'mark_read') {
        row.className = trim(row.className.replace('new_message', ''));
      } else {
        row.className = trim(row.className.replace(/ ?new_message ?|$/, ' new_message '));
      }
    }
  }  
  var ajax;
  if(reload_needed){
    ajax = new Ajax(function(){document.location.reload()});
  } else {
    ajax = new Ajax(this.ajax_callback.bind(this));
  }
  ajax.post('/inbox/ajax/ajax.php', post);
  this.update_status_buttons();
  var message_selector = ge('message_selector');
  message_selector ? message_selector.selectedIndex=0 : false;
  return false;
}

// Callback when an AJAX call comes back
megaboxx.prototype.ajax_callback = function(obj, text) {
  eval('result='+text);
  if (typeof result=='undefined') {
    return;
  }
  if (result=='refresh') {
    document.location.reload();
  }
  if (typeof(result.nav) != 'undefined' && (megabox_pager = ge('megabox_pager'))) {
    ge('megaboxx_pager').innerHTML=result.nav;
  }
  if (typeof(result.rows) != 'undefined') {
    if (result.rows) {
      // Create a tbody to put the innerHTML in
      var tbody = document.createElement('tbody');
      var boxx = ge('megaboxx');
      boxx.appendChild(tbody); // we have to append this first, then do innerHTML
      tbody.innerHTML = result.rows;

      // Now go through, pull out the dom nodes from that tbody we just made and append them to the first one
      var rows = tbody.getElementsByTagName('tr');
      var first_tbody = ge('megaboxx').getElementsByTagName('tbody')[0];
      while (rows.length) {
        first_tbody.appendChild(rows[0]);
      }
      tbody.parentNode.removeChild(tbody);
    }

    // Get rid of the loading icon
    ge('loading_boxx').style.display = 'none';
  }
  if (typeof(result.top_nav) != 'undefined') {
    ge('nav_inbox').innerHTML = result.top_nav;
  }
}

// Called when the user clicks on of the checkboxes
megaboxx.prototype.selection_onchange = function(obj) {
  this.update_status_buttons();
}

// Gets the status of a message from its row. Note, this doesn't relate to the status supplied in tdata
megaboxx.prototype.get_status = function(row) {
  if (row.className.indexOf('new_message') != -1) {
    return this.STATUS_UNREAD;
  } else {
    return this.STATUS_READ;
  }
}

megaboxx.prototype.get_thread_id = function(row) {
  return /thread_(\d+)/.exec(row.id)[1];
}

megaboxx.prototype.is_selected = function(row) {
  var inputs = row.getElementsByTagName('input');
  return inputs.length && inputs[0].checked;
}

megaboxx.prototype.get_selected_threads = function() {
  var rows = ge('megaboxx').getElementsByTagName('tr');
  var threads = [];

  for (var i=0; i<rows.length; i++) {
    if (this.is_selected(rows[i])) {
      threads.push(this.get_thread_id(rows[i]));
    }
  }
  return threads;
}

megaboxx.prototype.set_selection = function(status) {
  var rows = ge('megaboxx').getElementsByTagName('tr');
  var threads = [];

  for (var i=0; i<rows.length; i++) {
    if (!status || this.get_status(rows[i]) == status) {
      threads.push(this.get_thread_id(rows[i]));
    }
  }
  this.select_threads(threads, true);
}

// Selects thread rows from thread ids. If the 2nd parameter is true, old selections are cleared first
megaboxx.prototype.select_threads = function(threads, set/* = true*/) {
  var rows = ge('megaboxx').getElementsByTagName('tr');

  for (var i=0; i<rows.length; i++) {
    if (array_indexOf(threads, this.get_thread_id(rows[i])) != -1) {
      rows[i].getElementsByTagName('input')[0].checked = true;
    } else if (set) {
      rows[i].getElementsByTagName('input')[0].checked = false;
    }
  }

  this.update_status_buttons();
}

// Updates the enabled \ disabled status of the megaboxx buttons.
megaboxx.prototype.update_status_buttons = function() {
  var buttons = ge('inbox_status_buttons');
  if (!buttons) {
    return;
  }
  var threads = this.get_selected_threads();
  var unread_disabled = true;
  var read_disabled = true;

  for (var i=0;i<threads.length;i++) {
    var status = this.get_status(ge('thread_'+threads[i]));
    if (status == this.STATUS_UNREAD) {
      read_disabled = false;
    } else {
      unread_disabled = false;
    }
  }

  var message_selector = ge('message_selector');
  if (!threads.length && message_selector) {
    message_selector.selectedIndex = 0;
  }

  var delete_disabled = read_disabled && unread_disabled;
  var li = buttons.getElementsByTagName('li');
  var loop = [{l:li[0],d:unread_disabled}, {l:li[1],d:read_disabled}, {l:li[2],d:delete_disabled}];
  for (var i=0; i<loop.length; i++) {
    if (loop[i].l) {
      loop[i].l.className = trim(loop[i].l.className.replace('menu_disabled', '')) + (loop[i].d ? ' menu_disabled' : '');
    }
  }
}

megaboxx.prototype.create_hidden_input_helper = function(name, value) {
  var new_input = document.createElement('input');
  new_input.name = name;
  new_input.value = value;
  new_input.type = 'hidden';
  return new_input;
}

// Check the composer to make sure everything is good
megaboxx.prototype.submit_prehook = function(obj, inline, captcha_input) {
  var form = ge('compose_message');
  var length = trim(form.message.value).length;
  var error_text = null;
  var ids = ge('ids');

  // Sanity checking
  if (length == 0 && !ge('attachment')) {
    error_text = 'You may not send a message without a body.';
  } else if (length > 10000) {
    error_text = 'Your message is too long. Please shorten your message and try again.';
  } else if (ids && tokenizer.is_empty(ids)) {
    error_text = 'You must specifiy at least one recipient for this message.';
  }

  if (error_text) {
    var error = ge('error');
    if (error) {
      error.parentNode.removeChild(error);
    }
    error = document.createElement('div');
    error.id = 'error';
    error.innerHTML = '<h2>' + error_text + '</h2>';
    form.insertBefore(error, form.firstChild);
    return false;
  }

  // Captcha
  if (typeof captcha_html != 'undefined' && !captcha_input) {
    (new pop_dialog('captcha')).show_choice('Security Check', captcha_html, 'Submit', function() {
      megaboxx.submit_prehook(obj, inline, [ge('captcha_response'), ge('captcha_challenge_code')]);
    });
    return false;
  }

  if (form.rand_id.value == 0) {
    form.rand_id.value = Math.floor((Math.random() * 100000000));
  }

  // We have to rename app inputs before we submit, regardless of whether it's inlined
  if (attachments) {
    attachments.fix_app_inputs_on_send();
  }

  // Submit!
  if (inline) {
    var form = obj.form;
    var post = {action:'send_reply', id:form.thread.value, message:form.message.value};
    if (captcha_input) {
      post['captcha_response'] = captcha_input[0].value;
      post['captcha_challenge_code'] = captcha_input[0].value;
    }
    var attachment = ge('attachment');
    var inputs = [];
    if (attachment) {
      inputs = attachment.getElementsByTagName('input');
      for (var i=0; i<inputs.length; i++) {
        post[inputs[i].name] = inputs[i].value;
      }
    }

   // classifieds checkbox
   if (form.extra && form.extra.checked) {
     post['extra'] = form.extra.value;
   }
 
    var ajax = new Ajax();
    ajax.onDone = function(idontcare, text) {
      // Add the message
      var thread = ge('messages');
      var msg = document.createElement('div');
      set_inner_html(msg, text);
      thread.appendChild(msg);
      for (var i in inputs) {
        inputs[i].disabled = false;
      }
      form.getElementsByTagName('textarea')[0].value = '';

      // Make everything read
      var i = 0;
      var msg = null;
      while (msg = ge('msg_'+(i++))) {
        remove_css_class_name(msg, 'unread');
      }

      // Kill the attachment
      var attachment = ge('attachment');
      if(attachment) {
        attachment.parentNode.removeChild(attachment.nextSibling, true);
        attachment.parentNode.removeChild(attachment, true);
      }
      megaboxx.enable_all_attachment_forms();
    }
    ajax.post('/inbox/ajax/ajax.php', post);

    inputs = [form.getElementsByTagName('textarea')[0]];
    var form_inputs = form.getElementsByTagName('input');
    for (var i=0; i<form_inputs.length; i++) {
      if (form_inputs[i].type == 'button') {
        inputs.push(form_inputs[i]);
      }
    }
    for (var i in inputs) {
      inputs[i].disabled = true;
    }
  } else if (captcha_input) { // we need to manually submit the form
    var form = ge('compose_message');
    var span = document.createElement('span');
    span.innerHTML = ['<input type="hidden" name="captcha_response" value="', htmlspecialchars(captcha_input[0].value), '" />',
                      '<input type="hidden" name="captcha_challenge_code" value="', captcha_input[1].value, '" />'].join('');
    form.appendChild(span);
    form.onsubmit = null;
    form.submit();
  }
  return !inline;
}

// Delete a thread from threadview
megaboxx.prototype.submit_delete = function(obj) {
  (new pop_dialog).show_choice('Delete Thread', 'Are you sure you want to delete this thread?', 'Delete',
                               function() {
                                 var span = document.createElement('span');
                                 span.innerHTML = '<input name="delete" type="hidden" value="1" />' +
                                                  '<input name="folder" type="hidden" value="'+megaboxx_data.folder+'" />' +
                                                  '<input name="time" type="hidden" value="'+megaboxx_data.time+'" />';
                                 obj.appendChild(span);
                                 var form = obj.getElementsByTagName('input')[0].form;
                                 if (megaboxx_data.folder) {
                                   form.action += '?f=1';
                                 }
                                 form.submit();
                               },
                               'Cancel',
                               function() {
                                 generic_dialog.get_dialog(this).hide();
                               });
  return false;
}

megaboxx.prototype.add_attachment_stage = function(html) {
  var attachment = this.add_attachment_element(false);
  set_inner_html(attachment, html);
  var scroll = ge('scroll_here');
  if (scroll && scroll.className) {
    if (scroll.tagName.toLowerCase() == 'a') {
      scroll.parentNode.removeChild(scroll);
    } else {
      scroll.id = '';
    }
  }
  ge('attachment_stage_area').parentNode.id = 'scroll_here';
  this.scrolled = false;
  this.scroll_thread();
}

megaboxx.prototype.add_attachment_element = function(is_app) {

  this.disable_all_attachment_forms();
 
  var dts = ge('compose_message').getElementsByTagName('dt');
  var caret = dts[dts.length-1];

  var dt = document.createElement('dt');
  dt.innerHTML = '&nbsp;'
  var dd = document.createElement('dd');
  dd.className = 'share_stage';
  dd.innerHTML = '&nbsp';
  dd.id = 'attachment';
  dd.is_app = is_app;

  caret.parentNode.insertBefore(dt, caret);
  caret.parentNode.insertBefore(dd, caret);
  this.attachment_id++;
  
  return dd;
}

megaboxx.prototype.attachment_id = 0;

// Remove an attachment from the composer
megaboxx.prototype.remove_attachment = function(obj) {
  obj = obj.parentNode.parentNode.parentNode;
  obj.parentNode.removeChild(obj.previousSibling);
  obj.parentNode.removeChild(obj);

  this.enable_all_attachment_forms();
  megaboxx.share_added = false;
  return false;
}

megaboxx.prototype.toggle_all_attachment_forms = function(value) {
  (value ? hide : show)(ge('dd_attachment'));
  (value ? hide : show)(ge('dt_attachment'));
}

megaboxx.prototype.enable_all_attachment_forms = function() {
  this.toggle_all_attachment_forms(false);
}

megaboxx.prototype.disable_all_attachment_forms = function() {
  this.toggle_all_attachment_forms(true);
}

megaboxx.prototype.reset_rand_id = function() {
  try {
    ge('compose_message').rand_id.value = 0;
  } catch(e){};
}

megaboxx.prototype.scroll_thread = function() {
  var scroll_here = ge('scroll_here');
  var composer = ge('compose_message');
  if (!scroll_here || !composer || this.scrolled) {
    return;
  }
  this.scrolled = true;

  var wh = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight;
  var dh = elementY(composer) + composer.offsetHeight;
  var ey = elementY(scroll_here) - 100;
  var bh = Math.max(document.documentElement.scrollTop, document.body.scrollTop);

  if (dh - ey < wh) {
    ey -= (wh + ey) - dh;
  }
  if (dh < wh + 100) {
    return;
  }

  var obj = {dy:ey, i:750, cy:0, bh: bh, st:(new Date()).getTime()};
  obj.h = setInterval(function() {
    var t = (new Date()).getTime();
    var s = null;
    if (t > this.i + this.st) {
      s = this.dy;
      clearInterval(this.h);
    } else {
      var p = (t - this.st) / this.i;
      s = (this.dy - this.bh) * (1-Math.pow(1-Math.sin(Math.PI / 2 * p),2)) + this.bh;
    }
    if ((this.ls1 && this.ls1 != document.documentElement.scrollTop) ||
        (this.ls2 && this.ls2 != document.body.scrollTop)) {
      clearInterval(this.h);
    } else {
      document.documentElement.scrollTop = document.body.scrollTop = s;
      this.ls1 = document.documentElement.scrollTop;
      this.ls2 = document.body.scrollTop;
    }
  }.bind(obj), 25);
}

megaboxx.prototype.STATUS_ALL = 0;
megaboxx.prototype.STATUS_READ = 1;
megaboxx.prototype.STATUS_UNREAD = 2;
megaboxx.prototype.STATUS_NONE = -1;
var megaboxx = new megaboxx();

//
// tokenizer hooks
// =======================================================================================
if (typeof tokenizer != 'undefined') {
  tokenizer.prototype.onselect = function() {
    megaboxx.reset_rand_id();
  }
}
