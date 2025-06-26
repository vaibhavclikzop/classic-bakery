<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Software</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>
<div class="card">
    <div class="card-body">
        <div class="alert alert-danger" role="alert">
            <h4>Read carefully</h4>
            <ol>
                <li>This action can not be revoke.</li>
                <li>All related records will be removed forever.</li>
                <li>You will not be able to restore any deleted information.</li>
            </ol>

        </div>

        <div>
            <button class="btn btn-danger float-end" type="button" data-bs-toggle="modal"
                data-bs-target="#exampleModal">Reset</button>
            <a class="btn btn-success float-end mx-2" href="/">Close</a>
        </div>

    </div>

</div>

</html>
<form action="{{ route('ResetSoftware') }}" method="POST">
    @csrf
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Reset Software</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden"name="reset_key" value="{{ $reset_key->reset_key }}">
                    Are you sure you want to reset software?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reset Software</button>
                </div>
            </div>
        </div>
    </div>
</form>
