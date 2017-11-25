var report = {
    runQuery: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});
        $('#csvExport').val(0);
        $('#createChart').val(0);
        $("#msg").html('');

        $.ajax({
            url: $("#selectQueryForm").attr('action'),
            method: "POST",
            data: $("#selectQueryForm").serialize() + '&rules=' + JSON.stringify(rules)
        }).done(function (data) {
            $('#query-results').html(data);
            $('#msg').html('');
            $('#chart-container').addClass('hidden');
            $('html, body').animate({scrollTop: $('#query-results').offset().top}, 0);
        }).fail(report.handleErrorMsg);
    },
    exportCsv: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});

        $('#csvExport').val(1);
        $('#createChart').val(0);
        $('#rules').val(JSON.stringify(rules));
        $("#msg").html('');

        if ($("select[name='selectQueryFields[]'] option:selected").length > 0) {
            document.forms['selectQueryForm'].submit();
        } else {
            $("#msg").html('<div class="alert alert-danger">No Select Fields were chosen for the Query</div>');
        }
    },
    createChart: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});
        $('#csvExport').val(0);
        $('#createChart').val(1);
        $("#msg").html('');
        $("#query-results").html('');

        $.ajax({
            url: $("#selectQueryForm").attr('action'),
            method: "POST",
            data: $("#selectQueryForm").serialize() + '&rules=' + JSON.stringify(rules)
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.noData === true) {
                $('#query-results').html(response.noDataMsg);
                $('#chart-container').html('');
                $('html, body').animate({scrollTop: $('#query-results').offset().top}, 0);
            } else {
                $('#chart-container').removeClass('hidden');
                var chartType = $('#chart-type').val();
                switch (chartType) {
                    case 'bar':
                        $('#query-results').html(response.data);
                        report.createBarChart();
                        break;
                    case 'line':
                        report.createLineChart(response.data);
                        break;
                    case 'pie':
                        report.createPieChart(response.data);
                        break;
                }
                $('html, body').animate({scrollTop: $('#chart-container').offset().top}, 0);
            }
        }).fail(report.handleErrorMsg);
    },
    handleErrorMsg: function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.responseText !== undefined) {
            $('#msg').html(jqXHR.responseText);
        } else {
            $('#msg').html(errorThrown);
        }
        $('#chart-container').html('');
        $('#query-results').html('');
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
        var chartData = data;
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
    createPieChart: function (data, container, title) {
        container = container !== undefined ? container : 'chart-container';
        title = title !== undefined ? title : $('#chart-title').val();
        Highcharts.chart(container, {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                name: 'Award Share',
                colorByPoint: true,
                data: data
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
            'not_ends_with'
        ];
        var dateOperators = [
            'equal',
            'not_equal',
            'less',
            'less_or_equal',
            'greater',
            'greater_or_equal'
        ];
        var builder = $("#builder").queryBuilder({
            plugins: ['bt-tooltip-errors'],

            filters: [
                {
                    id: "AwardLabel",
                    label: "Award Type",
                    type: "string",
                    input: "select",
                    values: {
                        "Employee of the Week": "Employee of the Week",
                        "Employee of the Month": "Employee of the Month",
                        "Honorable Mention": "Honorable Mention"
                    },
                    operators: stringOperators

                },
                {
                    id: "AwardDate",
                    label: "Award Date",
                    type: "date",
                    operators: dateOperators,
                    plugin: 'datepicker',
                    plugin_config: {
                        format: 'yyyy-mm-dd',
                        todayBtn: 'linked',
                        todayHighlight: true,
                        autoclose: true
                    }
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
                    operators: dateOperators,
                    plugin: 'datepicker',
                    plugin_config: {
                        format: 'yyyy-mm-dd',
                        todayBtn: 'linked',
                        todayHighlight: true,
                        autoclose: true
                    }
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
    }
};
