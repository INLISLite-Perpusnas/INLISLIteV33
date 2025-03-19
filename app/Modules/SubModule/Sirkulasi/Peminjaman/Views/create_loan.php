<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Create Loan</h1>
    <form id="loanForm">
        <label for="member_id">Member ID:</label>
        <input type="text" id="member_id" name="member_id" required><br><br>

        <label for="branch_id">Branch ID:</label>
        <input type="text" id="branch_id" name="branch_id" required><br><br>

        <label for="collection_id">Collection ID:</label>
        <input type="text" id="collection_id" name="collection_id" required><br><br>

        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required><br><br>

        <button type="button" onclick="submitLoanForm()">Submit</button>
    </form>

    <div id="result"></div>

    <script>
        function submitLoanForm() {
            var formData = {
                member_id: $('#member_id').val(),
                branch_id: $('#branch_id').val(),
                collection_id: $('#collection_id').val(),
                user_id: $('#user_id').val()
            };

            $.ajax({
                url: '<?= base_url('api/sirkulasi-peminjaman/CreateLoan') ?>',
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(formData),
                success: function(response) {
                    $('#result').html('<p style="color: green;">' + response.message + '</p>');
                },
                error: function(xhr, status, error) {
                    $('#result').html('<p style="color: red;">Error: ' + xhr.responseJSON.message + '</p>');
                }
            });
        }
    </script>
</body>
</html>
