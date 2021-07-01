<div class="mt-2" id="saleChart" style="width:100%; height:400px;"></div>
<div id="revenueChart" style="width:100%; height:400px;"></div>

<div class="row">
    <div class="col">
        <h4 class="mt-3 mb-3">Please, select dates to draw charts for that period.</h4>
        <form>
            <div class="row">
                <div class="col">
                    <div id="fromDatepicker">
                    </div>
                    <input type="text" id="fromDate" name="fromDate">
                </div>
                <div class="col">
                    <div id="toDatepicker">
                    </div>
                    <input type="text" id="toDate" name="toDate">
                </div>
            </div>
        </form>
        <div class="mb-5"><button class="btn btn-primary mt-3 mb-1" id="sendData">Draw</button></div>
    </div>
</div>

<style>
    h5 {
        width: 272px;
        padding: 12px;
        color: white;
        background-color: red;
        border-radius: 3px;
    }

    input {
        width: 272px;
        padding: 4px;
        border: 1px solid #ccc;
        border-top: none;
        border-radius: 2px;
        color: #444;
    }
</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {

        $('#fromDatepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            defaultDate: new Date(),
            onSelect: function(date, dPicker) {
                $('#fromDate').val(date);
            }
        });

        $('#toDatepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            defaultDate: new Date(),
            onSelect: function(date, dPicker) {
                $('#toDate').val(date);
            }
        });

        var makeDateStr = function(date) {
            var format = function(part) {
                part = part > 9 ? part : '0' + part;
                return part;
            }

            var key = [];
            key.push(date.getFullYear());
            key.push(format(date.getMonth() + 1));
            key.push(format(date.getDate()));
            var keyStr = key.join('-');
            return keyStr;
        }

        var fetchSalesVolumeData = function(defaultDates) {
            var dates = $('form').serialize();
            if (defaultDates) {
                dates = defaultDates;
            }

            $.ajax({
                url: '/dashboard',
                type: 'POST',
                data: dates
            }).done(function(res) {
                $('h5').remove();

                if (res.error) {
                    $('h4').prepend($('<h5></h5>').append(res.error));
                    return;
                }

                Highcharts.chart('saleChart', {
                    title: {
                        text: 'Sales volume'
                    },
                    xAxis: {
                        categories: res.dates
                    },
                    yAxis: {
                        title: {
                            text: 'Number of orders/ unique customers'
                        }
                    },
                    series: [{
                        name: 'Orders',
                        data: res.numOfOrders
                    }, {
                        name: 'Unique Customers',
                        data: res.numOfUniqueCustomers
                    }]
                });

                Highcharts.chart('revenueChart', {
                    title: {
                        text: 'Revenue'
                    },
                    xAxis: {
                        categories: res.dates
                    },
                    yAxis: {
                        title: {
                            text: 'Revenue SEK'
                        }
                    },
                    series: [{
                        name: 'Revenue',
                        data: res.revenues
                    }]
                });
            }).fail(function(jqXHR, textStatus) {
                alert(jqXHR.statusText);
            });

        };

        var d = new Date();
        var fDayOfPrevMonth = new Date(d.getFullYear(), d.getMonth() - 1, 1);
        var lDayOfPrevMonth = new Date(d.getFullYear(), d.getMonth(), 0);
        var dates = {
            fromDate: makeDateStr(fDayOfPrevMonth),
            toDate: makeDateStr(lDayOfPrevMonth)
        };

        fetchSalesVolumeData(dates);

        $('#sendData').on('click', function() {
            fetchSalesVolumeData();
        });

    });
</script>