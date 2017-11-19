var report = {
    runQuery: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});
        $('#csvExport').val(0);
        $('#createChart').val(0);
        $.ajax({
            url: $("#selectQueryForm").attr('action'),
            method: "POST",
            data: $("#selectQueryForm").serialize() + '&rules=' + JSON.stringify(rules)
        }).done(function (data) {
            $('#query-results').html(data);
            $('#chart-container').addClass('hidden');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#query-results').html(jqXHR.responseText);
            } else {
                $('#query-results').html(errorThrown);
            }
        });
    },
    exportCsv: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});

        $('#csvExport').val(1);
        $('#createChart').val(0);
        $('#rules').val(JSON.stringify(rules));

        document.forms['selectQueryForm'].submit();
    },
    createChart: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});
        $('#csvExport').val(0);
        $('#createChart').val(1);
        $.ajax({
            url: $("#selectQueryForm").attr('action'),
            method: "POST",
            data: $("#selectQueryForm").serialize() + '&rules=' + JSON.stringify(rules)
        }).done(function (data) {
            $('#chart-container').removeClass('hidden');
            var chartType = $('#chart-type').val();
            switch (chartType) {
                case 'bar':
                    $('#query-results').html(data);
                    report.createBarChart();
                    break;
                case 'line':
                    report.createLineChart(data);
                    break;
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText !== undefined) {
                $('#query-results').html(jqXHR.responseText);
            } else {
                $('#query-results').html(errorThrown);
            }
        });
    },
    createBarChart: function () {
        // Modified from highcharts demo
        // http://jsfiddle.net/gh/get/library/pure/highcharts/highcharts/tree/master/samples/highcharts/demo/column-parsed/
        Highcharts.chart('chart-container', {
            data: {
                table: 'datatable'
            },
            chart: {
                type: 'column'
            },
            title: {
                text: $('#chart-title').val()
            },
            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Units (each)'
                }
            },
            tooltip: {
                formatter: function () {
                    var name;
                    if (this.point.name === undefined) {
                        var groupBy = $('#group-by-1').val();
                        if (groupBy === 'award-date' || groupBy === 'awardee-hire-date') {
                            var date = new Date(this.point.x);
                            name = date.toDateString();
                        } else {
                            name = this.point.x;
                        }
                    } else {
                        name = this.point.name;
                    }
                    return '<b>' + this.series.name + '</b><br/>' +
                        name + ': ' + this.point.y;
                }
            }
        });
    },
    createLineChart: function (data) {
        var chartData = JSON.parse(data);
        Highcharts.chart('chart-container', {
            title: {
                text: $('#chart-title').val()
            },
            xAxis: {
                categories: chartData.categories
            },
            yAxis: {
                title: {
                    text: 'Award Count'
                }
            },
            series: [{
                name: 'Award Count',
                data: chartData.data
            }]
        });
    },
    init: function () {
        var stringOperators = [
            'equal',
            'not_equal',
            'begins_with',
            'not_begins_with',
            'contains',
            'not_contains',
            'ends_with',
            'not_ends_with',
            // 'is_empty',
            // 'is_not_empty',
            // 'is_null',
            // 'is_not_null'
        ];
        var dateOperators = [
            'equal',
            'not_equal',
            'less',
            'less_or_equal',
            'greater',
            'greater_or_equal',
            // 'between',
            // 'not_between',
            // 'is_null',
            // 'is_not_null'
        ];
        var builder = $("#builder").queryBuilder({
            filters: [
                {
                    id: "AwardLabel",
                    label: "Award Type",
                    type: "string",
                    operators: stringOperators

                },
                {
                    id: "AwardDate",
                    label: "Award Date",
                    type: "date",
                    operators: dateOperators
                },
                {
                    id: "Email",
                    label: "Awardee Email",
                    type: "string",
                    operators: stringOperators
                },
                {
                    id: "fName",
                    label: "Awardee First Name",
                    type: "string",
                    operators: stringOperators
                },
                {
                    id: "lName",
                    label: "Awardee Last Name",
                    type: "string",
                    operators: stringOperators
                },
                {
                    id: "hireDate",
                    label: "Awardee Hire Date",
                    type: "date",
                    operators: dateOperators
                },
                {
                    id: "GiverFirstName",
                    label: "Giver First Name",
                    type: "string",
                    operators: stringOperators
                },
                {
                    id: "GiverLastName",
                    label: "Giver Last Name",
                    type: "string",
                    operators: stringOperators
                },
                {
                    id: "GiverEmail",
                    label: "Giver Email",
                    type: "string",
                    operators: stringOperators
                }
            ]
        });

        $('#builder').queryBuilder('setRules', testRules1);
    }
};

var testRules1 = {
    "condition":"AND",
    "rules":[
        {"id":"lName","field":"lName","type":"string","input":"text","operator":"not_begins_with","value":"z"}
    ],
    "valid":true
};
