<!doctype html>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">



        <link rel="icon" href="https://{{ $envs['BE_HOST'] }}/favicon.ico">



        <!-- Custom fonts for this template-->
        <link rel="stylesheet" href="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/vendor/fontawesome-free/css/all.min.css" type="text/css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">

        <!-- Custom styles for this template-->
        <link rel="stylesheet" href="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/css/sb-admin-2.min.css">



        <!-- Solenoid/HTTP -->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/lib/solenoid/solenoid.http.js"></script>
    
        <!-- Solenoid/File -->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/lib/solenoid/solenoid.file.js"></script>



        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">



		%sveltekit.head%
	</head>
	<body>
		%sveltekit.body%



        <!-- Bootstrap core JavaScript-->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/vendor/jquery/jquery.min.js"></script>
        <script src="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="https://{{ $envs['BE_HOST'] }}/assets/tpl/sb-admin-2/vendor/chart.js/Chart.min.js"></script>
	</body>
</html>