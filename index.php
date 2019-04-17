<!DOCTYPE html>
<html lang="en">
<title>URL shortener</title>
<meta name="robots" content="noindex, nofollow">
<body>
<form method="post" action="shorten.php" id="shortener">
    <label>URL to shorten <input type="text" name="url" id="url"></label> <input type="submit" value="Shorten">
</form>
<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
<script>
    (function ($) {
        $('#shortener').submit(function (e) {
            $.post('shorten.php', {url: $('#url').val()}, function (data) {
                $('#url').val(data);
            });
            e.preventDefault();
        });
    })(jQuery);
</script>
</body>
</html>
