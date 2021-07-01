<h1 class="mt-5"><?php echo $view_h1; ?></h1>

<p class="mt-5">Data generation can take from 10 seconds up to 8 minutes, depending on your computer!</p>
<div>
    <button id="generateData" class="btn btn-primary">Generate test data</button>
</div>

<div class="spinner-border text-primary mt-2 d-none" role="status">
    <span class="sr-only"></span>
</div>
<p id="progress" class="mt-3"></p>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {

        $('#generateData').on('click', function() {
            $('.spinner-border').removeClass('d-none');
            $.post('/datageneration', {}, function(res) {
                $('#progress').append(res.message);
                $('.spinner-border').addClass('d-none');
            });
        });

    });
</script>