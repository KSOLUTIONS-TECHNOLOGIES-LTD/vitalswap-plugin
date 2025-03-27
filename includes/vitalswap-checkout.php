<html>
    <head>
        <script src="https://scripts.vitalswap.com/checkout.js" crossorigin="anonymous"></script>

    </head>
    
    <body onload=loadVitalswap()>
        <script>
        let url = new URL(window.location);
        console.log(window.location)
const session_id  = url.searchParams.get("sId");
const isOTP = url.searchParams.get("isOTP");
const email  = url.searchParams.get("email");
console.log(session_id);
        function loadVitalswap(){
            vitalswapCheckout.init({
				session: session_id,
				isOtp: false, 
				email: email, 
				// callback: "https://yourcheckoutpage.com", //Optional URL to redirect after completion
				environment: "sandbox", // Set to "sandbox" or "production"
				onload: () => {
					console.log("Checkout started");
				},
				onsuccess: (data) => {
					console.log("Checkout successful:", data);
				},
				onclose: (data) => {
					console.log("Checkout closed:", data);
				},
				onerror: (error) => {
					console.error("Checkout error:", error);
				},
			});
        }
        </script>
    </body>
</html>