// spipu-dashboard/google-graph-plots.js

class GoogleGraphPlots {
    constructor(
        destinationId,
        dateFrom,
        dateTo,
        source,
        forceMinToZero,
        dateFormat = "yyyy-MM-dd HH:mm",
        margeBottom = 60,
    ) {
        this.destinationId  = destinationId;
        this.dateFrom       = dateFrom;
        this.dateTo         = dateTo;
        this.source         = source;
        this.forceMinToZero = forceMinToZero;
        this.dateFormat     = dateFormat;
        this.margeBottom    = margeBottom;

        this.init();
    }

    init() {
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(
            $.proxy(this.initDraw, this)
        );
        return true;
    }

    initDraw() {
        $(window).resize($.proxy(this.draw, this));

        this.draw();
    }

    draw() {
        let graphChartData = new google.visualization.DataTable();
        graphChartData.addColumn('datetime', 'Date');
        graphChartData.addColumn('number', 'Value');

        let rows = [];
        for (let key in this.source) {
            let row = [];
            row.push(this.getDateFromString(this.source[key]['d']));
            row.push(this.source[key]['v'] ? this.source[key]['v'] : 0.);
            rows.push(row);
        }

        graphChartData.addRows(rows);

        let date_formatter = new google.visualization.DateFormat({pattern: this.dateFormat});
        date_formatter.format(graphChartData, 0);

        // prepare the options
        let graphChartOptions = {
            chartArea: {
                left:   60,
                right:  5,
                top:    5,
                bottom: this.margeBottom
            },
            series: [{targetAxisIndex:0}],
            vAxes: {
                0: {
                    textStyle: { fontSize: 10 },
                    titleTextStyle: { fontSize: 0 },
                    viewWindowMode: 'maximized'
                }
            },
            focusTarget: 'category',
            hAxis: {
                slantedText: true,
                textStyle: { fontSize: 10 },
                format: this.dateFormat,
                gridlines: { count: 10 },
                minorGridlines: { count: 2 },
                minValue: this.getDateFromString(this.dateFrom),
                maxValue: this.getDateFromString(this.dateTo)
            },
            explorer: null,
            legend: { position: 'none' }
        };

        if (this.forceMinToZero) {
            graphChartOptions.vAxes[0].minValue = 0;
        }

        let chart = new google.visualization.LineChart(document.getElementById(this.destinationId));
        chart.draw(graphChartData, graphChartOptions);
    }

    getDateFromString(value) {
        var date = value.replace(/[^0-9]/g, ' ').split(' ');

        return new Date(date[0], date[1]-1, date[2], date[3], date[4], date[5]);
    }
}

window.GoogleGraphPlots = GoogleGraphPlots;
