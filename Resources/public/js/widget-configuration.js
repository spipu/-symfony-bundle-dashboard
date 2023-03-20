// spipu-dashboard/widget-configuration.js

class WidgetConfiguration {
    constructor(refresherUrl)
    {
        this.refresherUrl = refresherUrl;
        this.init();
    }

    init()
    {
        let widgetItems = $('.widget-item');
        widgetItems.each((widgetItem, element) => {
            let identifier = $(element).data('id');
            this.addListeners(identifier)
        })
    }

    addListeners(widgetIdentifier)
    {
        let widgetItem = $(`.widget-item[data-id="${widgetIdentifier}"]`);
        widgetItem.find('button[data-widget-role="validate-configuration"]').on('click', () => {
            widgetItem.find(`#modalConfiguration${widgetIdentifier}`).modal('hide');

            let form = widgetItem.find('form[data-widget-role="configuration-form"]');
            let values = {};
            const $inputs = form.find(':input');
            $inputs.each(function () {
                values[this.name] = $(this).val();
            });

            this.refreshWidget(widgetIdentifier, values)
        })
    }

    refreshWidget(widgetIdentifier, values)
    {
        let widgetItem = $(`.widget-item[data-id="${widgetIdentifier}"]`);
        widgetItem.addClass('loading');
        fetch(
            `${this.refresherUrl}?identifier=${widgetIdentifier}&${this.buildQueryParameters(values)}`,
            {
                method: 'GET',
            }
        )
            .then((response) => {
                return response.text();
            })
            .then((html) => {
                let $replacement = $(html);
                $(`.widget-item[data-id="${widgetIdentifier}"]`).replaceWith($replacement);
                let initFunction = `initWidget_${widgetIdentifier}`;
                if (typeof window[initFunction] === 'function') {
                    window[initFunction]();
                }
                this.addListeners(widgetIdentifier);
                widgetItem.removeClass('loading');
            })
            .catch(function (err) {
                console.warn('Something went wrong.', err);
                widgetItem.removeClass('loading');
            });
    }

    buildQueryParameters(values)
    {
        let queryParameters = [];
        for (let key of Object.keys(values)) {
            if (Array.isArray(values[key])) {
                if (values[key].length === 0) {
                    queryParameters.push(`${key}=`);
                    continue;
                }
                values[key].forEach(val => queryParameters.push(`${key}=${val}`))
                continue;
            }
            queryParameters.push(`${key}=${values[key]}`);
        }
        return `${queryParameters.join('&')}`
    }
}

window.WidgetConfiguration = WidgetConfiguration;
