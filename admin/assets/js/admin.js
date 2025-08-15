jQuery(document).ready(function($) {
    'use strict';

    var form = $('#code-finder-form');
    var resultsContainer = $('#search-results-container');
    var spinner = form.find('.spinner');
    var submitButton = form.find('#submit');

    form.on('submit', function(e) {
        e.preventDefault();

        var searchTerm = form.find('#search_term').val();
        if (!searchTerm) {
            resultsContainer.html('<p>Please enter a search term.</p>');
            return;
        }

        $.ajax({
            url: codeFinder.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'code_finder_search',
                nonce: codeFinder.nonce,
                search_term: searchTerm,
                search_type: form.find('#search_type').val(),
                search_in: form.find('input[name="search_in[]"]:checked').map(function() {
                    return this.value;
                }).get()
            },
            beforeSend: function() {
                spinner.addClass('is-active');
                submitButton.prop('disabled', true);
                resultsContainer.html('');
            },
            success: function(response) {
                if (response.success) {
                    resultsContainer.html(response.data.html);
                    Prism.highlightAll(); // Apply syntax highlighting
                } else {
                    resultsContainer.html('<p>Error: ' + response.data.message + '</p>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                resultsContainer.html('<p>An unexpected error occurred: ' + textStatus + ' - ' + errorThrown + '</p>');
            },
            complete: function() {
                spinner.removeClass('is-active');
                submitButton.prop('disabled', false);
            }
        });
    });
});
