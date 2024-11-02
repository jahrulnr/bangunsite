@csrf
<input type="hidden" name="id" value="">
<div class="form-group mb-3">
    <label>Host</label>
    <input type="text" name="host" value="{{$host??''}}" placeholder="127.0.0.1" class="form-control" required>
</div>
<div class="form-group mb-3">
    <label>Port</label>
    <input type="number" min="1" name="port" value="{{$port??''}}" placeholder="22" class="form-control">
</div>
<div class="form-group mb-3">
    <label>User</label>
    <input type="text" name="user" value="{{$user??''}}" placeholder="bangunsite" class="form-control" required>
</div>
<div class="form-group mb-3">
    <label>Password</label>
    <input type="password" name="pass" value="{{$pass??''}}" placeholder="******" class="form-control" required>
</div>