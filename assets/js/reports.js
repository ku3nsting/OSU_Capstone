var report = {
    runQuery: function () {
        var rules = $("#builder").queryBuilder('getRules', {skip_empty: true});
        $('#csvExport').val(0);
        $.ajax({
            url: $("#selectQueryForm").attr('action'),
            method: "POST",
            data: $("#selectQueryForm").serialize() + '&rules=' + JSON.stringify(rules)
        }).done(function (data) {
            $('#query-results').html(data);
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
        $('#rules').val(JSON.stringify(rules));

        document.forms['selectQueryForm'].submit();
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

        // $('#builder').queryBuilder('setRules', testRules);
    }
};

var testRules = {
    "condition": "AND",
    "rules": [
        {
            "id": "AwardLabel",
            "field": "AwardLabel",
            "type": "string",
            "input": "text",
            "operator": "equal",
            "value": "Employee of the Month"
        },
        {
            "condition": "OR",
            "rules": [
                {
                    "id": "lName",
                    "field": "lName",
                    "type": "string",
                    "input": "text",
                    "operator": "begins_with",
                    "value": "A"
                },
                {
                    "id": "AwardDate",
                    "field": "AwardDate",
                    "type": "date",
                    "input": "text",
                    "operator": "greater",
                    "value": "01/01/2017"
                },
                {
                    "id": "lName",
                    "field": "lName",
                    "type": "string",
                    "input": "text",
                    "operator": "begins_with",
                    "value": "B"
                },
                {
                    "id": "lName",
                    "field": "lName",
                    "type": "string",
                    "input": "text",
                    "operator": "begins_with",
                    "value": "C"
                },
                {
                    "condition": "OR",
                    "rules": [
                        {
                            "id": "lName",
                            "field": "lName",
                            "type": "string",
                            "input": "text",
                            "operator": "begins_with",
                            "value": "D"
                        },
                        {
                            "id": "lName",
                            "field": "lName",
                            "type": "string",
                            "input": "text",
                            "operator": "begins_with",
                            "value": "E"
                        }
                    ]
                },
                {
                    "id": "AwardDate",
                    "field": "AwardDate",
                    "type": "date",
                    "input": "text",
                    "operator": "greater",
                    "value": "01/01/2017"
                }
            ]
        },
        {
            "id": "AwardDate",
            "field": "AwardDate",
            "type": "date",
            "input": "text",
            "operator": "greater",
            "value": "01/01/2017"
        }
    ],
    "valid": true
};