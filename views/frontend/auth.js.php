<?php
/**
 * @author    Alexandr Belogolovsky <ab2014box@gmail.com>
 * @copyright Copyright (c) 2016, Alexandr Belogolovsky
 */

    $this->registerJs("
        var data = jQuery('#expired-time').data();
        if (data) {
            var time = (new Date()).getTime() + (data.period * 1000);
            var result = new Date();
            result.setTime(time);
            jQuery('#expired-time').html(result);
        }
        
        jQuery('#auth-captcha').bind('click', function() {
            jQuery('#auth-captcha').attr('src', '{$assets->baseUrl}/img/wait.gif');
        });

        // json list
        jQuery('#list-json-button').bind('click', function() {
            jQuery('#progress').show();
            var jxhr = jQuery.ajax({
                url: this.href,
                type: 'GET',
                headers: {'Accept': 'application/json'},
                complete: function(jxhr, status) {
                    jQuery('#progress').hide();
                    alert('Result:' + status + '\\n' + JSON.stringify(jxhr.responseJSON))
                },
            });            
            return false;
        });

        // json list page #
        jQuery('#list-page-json-button').bind('click', function() {
            var page = jQuery('#page-number').val();
            if (!page) {
                alert('Page number required');
                return false;
            } else {
                url = this.href + '&page=' + page;
                jQuery('#progress').show();
                var jxhr = jQuery.ajax({
                    url: url,
                    type: 'GET',
                    headers: {'Accept': 'application/json'},
                    complete: function(jxhr, status) {
                        jQuery('#progress').hide();
                        alert('Result:' + status + '\\n' + JSON.stringify(jxhr.responseJSON))
                    },
                });
            }
            return false;
        });

        // view one
        jQuery('#view-button').bind('click', function() {
            var id = jQuery('#post-id').val();
            if (!id) {
                alert('Post ID required');
                return false;
            } else {
                var urlPattern = jQuery('#view-post-url-pattern').val();
                this.href = urlPattern.replace('{$patternId}', id);
            }
        });

        // view one JSON
        jQuery('#view-json-button').bind('click', function() {
            var id = jQuery('#post-id').val();
            if (!id) {
                alert('Post ID required');
                return false;
            } else {
                jQuery('#progress').show();
                var urlPattern = jQuery('#view-post-url-pattern').val();
                var url = urlPattern.replace('{$patternId}', id);
                var jxhr = jQuery.ajax({
                    url: url,
                    type: 'GET',
                    headers: {'Accept': 'application/json'},
                    complete: function(jxhr, status) {
                        jQuery('#progress').hide();
                        alert('Result:' + status + '\\nJSON:' + JSON.stringify(jxhr.responseJSON));
                    },
                });            
            }
            return false;
        });

        // delete
        jQuery('#delete-json-button').bind('click', function() {
            var id = jQuery('#post-id').val();
            if (!id) {
                alert('Post ID required');
            } else if (confirm('Are you sure to delete this post #' + id + '?')) {
                jQuery('#progress').show();
                var urlPattern = jQuery('#view-post-url-pattern').val();
                var url = urlPattern.replace('{$patternId}', id);
                var jxhr = jQuery.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {'Accept': 'application/json'},
                    complete: function(jxhr, status) {
                        jQuery('#progress').hide();
                        switch (jxhr.status) {
                            case 204:
                                alert('Post #' + id + ' deleted OK');
                                break;
                            case 404:
                                alert('Post #' + id + ' not found');
                                break;
                            case 403:
                                alert('Forbidden to delete post #' + id + '\\n' + JSON.stringify(jxhr.responseJSON));
                                break;
                            default:
                                alert('Result:' + status + '\\nJSON:' + JSON.stringify(jxhr));
                                break;
                        }
                    },
                });            
            }
            return false;
        });

        // clean and hide form
        jQuery('#clean-form-button').bind('click', function() {
            if (confirm('Are you sure to clean edit form?')) {
                jQuery('#text-edit').val('');
                jQuery('#edit-form').hide();
                jQuery('#post-id').val('');
                jQuery('#clean-form-button').hide();
            }
            return false;
        });

        // edit form load
        jQuery('#load-form-button').bind('click', function() {
            jQuery('#post-id-edit').html('...');
            jQuery('#text-edit').val('');
            var id = jQuery('#post-id').val();
            if (!id) {
                alert('Post ID required');
                return false;
            } else {
                jQuery('#progress').show();
                var urlPattern = jQuery('#view-post-url-pattern').val();
                var url = urlPattern.replace('{$patternId}', id);
                var jxhr = jQuery.ajax({
                    url: url,
                    type: 'GET',
                    headers: {'Accept': 'application/json'},
                    success: function(data, status, jxhr) {
                        jQuery('#progress').hide();
                        jQuery('#post-id-edit').html(id);
                        jQuery('#text-edit').val(data.text);
                        jQuery('#edit-form').show();
                        jQuery('#clean-form-button').show();
                    },
                    error: function(jxhr, status, errorThrown) {
                        jQuery('#progress').hide();
                        jQuery('#edit-form').hide();
                        alert('Post ' + id + ' not found or another problem\\n' + JSON.stringify(jxhr));
                    },
                });            
                return false;
            }
            return false;
        });
        
        // edit
        jQuery('#edit-form').bind('submit', function() {
            jQuery('#progress').show();
            var id = jQuery('#post-id-edit').html();
            var text = jQuery('#text-edit').val();
            var body = {'id': id, 'text': text};
            var url = this.action.replace('{$patternId}', id);
            var jxhr = jQuery.ajax({
                url: url,
                type: 'PUT',
                headers: {'Accept': 'application/json'},
                dataType: 'json',
                data: body,
                complete: function(jxhr, status) {
                    jQuery('#progress').hide();
                    alert('Result:' + status + '\\nJSON:' + JSON.stringify(jxhr.responseJSON));
                },
            });            
            return false;
        });

        // create
        //jQuery('#create-form').bind('submit', function() { alert(this.action); return false; });

    ");
