<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MAA JASOL PAINTS - Delete Account</title>
    <style>
        /* Basic styling */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff; /* Text color */
        }

        .container {
            background-color: #ffffff; /* White background for the container */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            transition: transform 0.2s; /* Adding a smooth hover effect */
        }

        .container:hover {
            transform: scale(1.02); /* Slightly increase size on hover */
        }

        h1 {
            color: #333;
            font-size: 26px; /* Increased font size for main heading */
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            color: #555;
            font-size: 14px;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px; /* Increased padding */
            margin-bottom: 20px;
            border: 2px solid #007bff; /* Blue border */
            border-radius: 5px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s; /* Transition for border color */
        }

        input[type="text"]:focus {
            border-color: #0056b3; /* Darker blue on focus */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s; /* Transition for background color */
        }

        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .alert {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da; /* Red background for error */
            color: #721c24; /* Red text color */
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            display: none;
        }

        .success {
            background-color: #d4edda; /* Green background for success */
            color: #155724; /* Green text color */
            border-color: #c3e6cb;
        }

    </style>
    <script>
        async function deleteAccount(event) {
            event.preventDefault();

            // Get the mobile number from the input field
            const mobileNumber = document.getElementById('mobile_number').value;
            const alertBox = document.getElementById('alert-box');

            // Clear any previous alerts
            alertBox.style.display = 'none';

            // Check if mobile number is provided
            if (!mobileNumber) {
                alertBox.innerHTML = "Please enter a mobile number.";
                alertBox.classList.remove('success');
                alertBox.classList.add('alert');
                alertBox.style.display = 'block';
                return;
            }

            try {
                // Make the DELETE request to the API
                const response = await fetch('https://admin.maajasolpaints.com/api/delete-account', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token
                        'Authorization': 'Bearer ' + localStorage.getItem('token') // If you're using Bearer token for authentication
                    },
                    body: JSON.stringify({
                        mobile_number: mobileNumber
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alertBox.innerHTML = data.message;
                    alertBox.classList.remove('alert');
                    alertBox.classList.add('success');
                } else {
                    alertBox.innerHTML = `Error: ${data.message}`;
                    alertBox.classList.remove('success');
                    alertBox.classList.add('alert');
                }

                alertBox.style.display = 'block';

            } catch (error) {
                console.error('Error:', error);
                alertBox.innerHTML = "An error occurred while deleting the account.";
                alertBox.classList.remove('success');
                alertBox.classList.add('alert');
                alertBox.style.display = 'block';
            }
        }

    </script>
</head>

<body>

    <div class="container">
        <h1>Delete Your Account</h1>
        <form onsubmit="deleteAccount(event)">
            <label for="mobile_number">Enter your Mobile Number:</label>
            <input type="text" id="mobile_number" name="mobile_number" placeholder="Enter your mobile number" pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" required>

            <button type="submit">Delete Account</button>
        </form>

        <!-- Alert box for success or error messages -->
        <div id="alert-box" class="alert"></div>
    </div>

</body>

</html>
