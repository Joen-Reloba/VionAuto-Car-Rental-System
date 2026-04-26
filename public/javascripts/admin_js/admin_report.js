function printReport() {
    const el = document.getElementById("report_type_data");
    const reportType = el.dataset.type;
    const dateFrom = el.dataset.from;
    const dateTo = el.dataset.to;

    const originalTitle = document.title;
    document.title = reportType + "_report_" + dateFrom + "_to_" + dateTo;

    window.print();

    document.title = originalTitle;
}
