jQuery( document ).ready( function($) {

    var colors = {
        google: '#F79C20',
        facebook: '#3C5A99',
        twitter: '#55ACEE',
        mailchimp: '#756154',
        youtube: '#E52D27',
        no: 'rgba(0,0,0,0)'
    }

    var datasets = [];

    if ( graph_php.status.google == 'enabled' ) {
        datasets.push({
            label: "Analytics",
            fill: false,
            borderJoinStyle: 'miter',
            borderColor: colors.google,
            backgroundColor: colors.google,
            pointBorderColor: colors.no,
            pointBackgroundColor: colors.no,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: colors.google,
            data: graph_php.data.google
        });
    }
    if ( graph_php.status.facebook == 'enabled' ) {
        datasets.push({
            label: "Facebook",
            fill: false,
            borderJoinStyle: 'miter',
            borderColor: colors.facebook,
            backgroundColor: colors.facebook,
            pointBorderColor: colors.no,
            pointBackgroundColor: colors.no,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: colors.facebook,
            data: graph_php.data.facebook
        });
    }
    if ( graph_php.status.twitter == 'enabled' ) {
        datasets.push({
            label: "Twitter",
            fill: false,
            borderJoinStyle: 'miter',
            borderColor: colors.twitter,
            backgroundColor: colors.twitter,
            pointBorderColor: colors.no,
            pointBackgroundColor: colors.no,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: colors.twitter,
            data: graph_php.data.twitter
        });
    }
    if ( graph_php.status.mailchimp == 'enabled' ) {
        datasets.push({
            label: "Mailchimp",
            fill: false,
            borderJoinStyle: 'miter',
            borderColor: colors.mailchimp,
            backgroundColor: colors.mailchimp,
            pointBorderColor: colors.no,
            pointBackgroundColor: colors.no,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: colors.mailchimp,
            data: graph_php.data.mailchimp
        });
    }
    if ( graph_php.status.youtube == 'enabled' ) {
        datasets.push({
            label: "YouTube",
            fill: false,
            borderJoinStyle: 'miter',
            borderColor: colors.youtube,
            backgroundColor: colors.youtube,
            pointBorderColor: colors.no,
            pointBackgroundColor: colors.no,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: colors.youtube,
            data: graph_php.data.youtube
        });
    }

        

    var data = {
        labels: graph_php.data.date,
        datasets: datasets
    };
    var options = {
        width: 400,
        height: 400
    };

    if ( $("#sts-graph").length > 0 ) {
        var ctx = $("#sts-graph");
        var g = new Chart( ctx, {
            type: 'line',
            data: data,
            options: options
        });
    }
});
