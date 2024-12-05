<!DOCTYPE html>
<html>
<body>
<script src="https://cdn.paddle.com/paddle/paddle.js"></script>
<script>
    Paddle.Setup(
        {
            vendor: @json(config('settings.paddle_vendor_id')),
            eventCallback: function (data) {
                if (data.event === "Checkout.Close") {
                    window.location.href = @json(route('subscription'));
                    return false;
                }
            }
        }
    );

    @if(config('settings.paddle_sandbox_mode') == 'Yes')
    Paddle.Environment.set('sandbox');
    @endif

    Paddle.Checkout.open({
        override: @json($redirect_url)
    });
</script>
</body>
</html>
