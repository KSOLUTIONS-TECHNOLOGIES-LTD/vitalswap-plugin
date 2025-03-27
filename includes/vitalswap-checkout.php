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
					alert("Payment successful")
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