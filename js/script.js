// js/script.js
jQuery(document).ready(function ($) {
    $('#kanban-ease-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $messages = $('#form-messages');
        const $submit = $form.find('button[type="submit"]');

        // Clear previous messages
        $messages.removeClass('success error').hide();

        // Disable submit button
        $submit.prop('disabled', true).text(kanbanEase.i18n.sending);

        // Validate agreement checkbox
        const $agreement = $form.find('[name="agreement"]');
        if (!$agreement.is(':checked')) {
            $messages
                .addClass('error')
                .html(kanbanEase.i18n.agree_required)
                .show();
            $submit.prop('disabled', false).text(kanbanEase.i18n.submit);
            return;
        }

        // Collect form data
        const formData = {
            form_id: $form.find('[name="form"]').val(),
            first_name: $form.find('[name="first_name"]').val(),
            last_name: $form.find('[name="last_name"]').val(),
            company_name: $form.find('[name="company_name"]').val(),
            email: $form.find('[name="email"]').val(),
            phone_number: $form.find('[name="phone_number"]').val(),
            approved: $('#agreement').is(':checked'),
        };

        // Send to WordPress REST API endpoint instead of directly to Kanban Ease
        $.ajax({
            url: wpApiSettings.root + 'kanban-ease/v1/submit-form',
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function (response) {
                $messages
                    .addClass('success')
                    .html(kanbanEase.i18n.success)
                    .show();
                $form[0].reset();
            },
            error: function (xhr) {
                let errorMessage = kanbanEase.i18n.error;

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $messages
                    .addClass('error')
                    .html(errorMessage)
                    .show();
            },
            complete: function () {
                $submit.prop('disabled', false).text(kanbanEase.i18n.submit);
            }
        });
    });
});