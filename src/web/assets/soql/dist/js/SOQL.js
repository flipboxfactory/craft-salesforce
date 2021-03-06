/** global: Craft */
/** global: Garnish */

Craft.ForceQuery = Garnish.Base.extend(
    {
        $results: null,
        $soql: null,
        $submit: null,
        $form: null,
        $container: null,

        init: function (form, settings) {
            this.$form = $(form);
            this.$container = this.$form.find("#query-preview");
            this.$results = this.$container.find("#query-results");
            this.$soql = this.$container.find("#query-soql");

            this.setSettings(settings, Craft.ForceQuery.defaults);

            this.$submit = this.$form.find('.preview');
            this.$submit.on('click', $.proxy(this, 'onSubmit'));

            var $spinner = this.$form.find('.spinner');
            if ($spinner.length) {
                this.$spinner = $spinner;
            } else {
                this.$spinner = $('<div class="spinner hidden"/>').insertBefore(this.$results);
            }
        },

        onSubmit: function (ev) {
            ev.preventDefault();

            this.$spinner.removeClass('hidden');

            Craft.actionRequest(
                'POST',
                this.settings.action,
                this.$form.serialize(),
                $.proxy(
                    function (response, textStatus, jqXHR) {
                        this.$spinner.addClass('hidden');
                        if (jqXHR.status >= 200 && jqXHR.status <= 299) {
                            this.afterQuery(response);

                            if (response.errors) {
                                Craft.cp.displayError(
                                    Craft.t('salesforce', this.settings.messageError)
                                );
                            } else {
                                Craft.cp.displayNotice(
                                    Craft.t('salesforce', this.settings.messageSuccess)
                                );
                            }
                        }
                    },
                    this
                )
            );
        },
        afterQuery: function (response) {
            console.log(this.$container);
            this.$container.show();
            this.$soql.html(response.query);
            this.$results.html(JSON.stringify(response.result || response.errors, null, 2));
            this.onAfterQuery(response);
        },

        onAfterQuery: function (response) {
            this.settings.onAfterQuery(response);
            this.trigger('afterQuery', {response: response});
        }
    },
    {
        defaults: {
            action: 'salesforce/cp/queries/request',
            messageError: "Failed to execute query",
            messageSuccess: "Query executed successfully",
            onAfterQuery: $.noop
        }
    }
);

