<!-- modal.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notification Modal</title>
    <!-- Include your CSS and JS files here -->
</head>
<body>
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Notification Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Ajax-loaded content will be inserted here -->
                <div class="container">
                    <div class="logo-container text-center">
                        <img class="logo" src="{{ asset('/logo/logo.png') }}" alt="" width="200px" height="50"/>
                    </div>
                    <div class="header">
                        <h1>Notification</h1>
                    </div>
                    <div class="content">
                        <p>Dear <span id="employeeName"></span>,</p>
                        <p id="mailMessage"></p>
                        <p class="signature">
                            Best regards,<br />
                            <span id="hrName"></span><br />
                            HR<br />
                            <span id="companyName"></span>
                        </p>
                    </div>
                    <div class="footer">
                        <div class="social-icons">
                            <a href="https://www.facebook.com/browntechint/" target="_blank">Facebook</a>
                            <a href="https://www.instagram.com/browntechint/?igshid=YmMyMTA2M2Y%3D" target="_blank">Instagram</a>
                            <a href="https://www.linkedin.com/company/browntech/" target="_blank">LinkedIn</a>
                        </div>
                        <p>&copy; 2024 <a href="https://www.browntech.co/" class="text-white" target="_blank">Brown Tech Int</a>. All rights reserved.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>





{{--<script>--}}
{{--    $(document).ready(function() {--}}
{{--        $('#openModalButton').click(function() {--}}
{{--            $.ajax({--}}
{{--                url: '{{ route('get.notification.modal') }}',--}}
{{--                method: 'POST',--}}
{{--                data: {--}}
{{--                    _token: '{{ csrf_token() }}', // Add this line to include the CSRF token--}}
{{--                },--}}
{{--                success: function(data) {--}}
{{--                    // Parse the JSON data received from the server--}}
{{--                    var notificationData = JSON.parse(data);--}}

{{--                    // Update the modal content with the received data--}}
{{--                    $('#employeeName').text(notificationData.employeeName);--}}
{{--                    $('#mailMessage').html(notificationData.mailMessage);--}}
{{--                    $('#hrName').text(notificationData.hrName);--}}
{{--                    $('#companyName').text(notificationData.companyName);--}}

{{--                    // Show the modal--}}
{{--                    $('#notificationModal').modal('show');--}}
{{--                },--}}
{{--                error: function(error) {--}}
{{--                    console.error('Error fetching modal content:', error);--}}
{{--                }--}}
{{--            });--}}

{{--        });--}}
{{--    });--}}
{{--</script>--}}
</body>
</html>
