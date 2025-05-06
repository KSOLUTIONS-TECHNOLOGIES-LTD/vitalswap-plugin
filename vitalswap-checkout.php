<html>

<head>
    <script src="https://scripts.vitalswap.com/checkout.js" crossorigin="anonymous"></script>

</head>

<body onload=loadVitalswap()>


    <script>
        let url = new URL(window.location);

        const session_id = url.searchParams.get("sId");
        const isOTP = url.searchParams.get("isOTP");
        const email = url.searchParams.get("email");
        const returnUrl = url.searchParams.get("returnUrl");
        const apiCk = url.searchParams.get("ck");
        const apiCs = url.searchParams.get("cs");
        const oid = url.searchParams.get("oid");

        async function processOrder() {
            const url = 'vitalswap_woo_order_status.php' + `?returnUrl=${returnUrl}&ck=${apiCk}&cs=${apiCs}&oid=${oid}`;
            try {
                const response = await fetch(url);
                if (!response.ok) {

                    throw new Error(`Response status: ${response.status}`);
                }

            } catch (error) {

                console.error(error.message);
            }
        }


        function loadVitalswap() {
            vitalswapCheckout.init({
                session: session_id,
                isOtp: false,
                email: email,
                callback: returnUrl,
                environment: "sandbox", // Set to "sandbox" or "production"
                onload: () => {
                    console.log("Checkout started");
                },
                onsuccess: (data) => {

                    processOrder();

                    window.location.href = returnUrl
                },
                onclose: (data) => {
                    window.location.href = returnUrl

                },
                onerror: (error) => {
                    console.error("Checkout error:", error);
                    alert("An error occurred, please try again")

                },
            });
        }
    </script>

</body>

</html>