@csrf
<div class="form-group mb-3">
    <label>Site Name</label>
    <input type="text" name="name" value="{{$name??''}}" placeholder="Site name" class="form-control">
</div>
<div class="form-group mb-3">
    <label>Domain</label>
    <input type="text" name="domain" value="{{$domain??''}}" placeholder="Domain name" class="form-control" required>
</div>
<div class="form-group mb-3">
    <label>Path</label>
    <input type="text" name="path" value="{{$path??''}}" placeholder="{{env('WEB_PATH')}}/" value="" class="form-control" required>
</div>
<div class="custom-control custom-switch mb-3">
    <input type="checkbox" name="ssl" class="custom-control-input" id="ssl" {{ isset($ssl) && $ssl ? 'checked': '' }}>
    <label for="ssl" class="custom-control-label">SSL</label>
</div>
<div class="custom-control custom-switch mb-3">
    <input type="checkbox" name="active" class="custom-control-input" id="active" {{ isset($active) && $active ? 'checked' : '' }}>
    <label for="active" class="custom-control-label">Enable</label>
</div>

@push('js')
<script>
    $(document).ready(function(){
        const inPath = $('input[name=path]')

        if (inPath.val().length == 0){
            inPath.val(inPath.attr('placeholder'))
            $('input[name=domain]').on('keyup', function(e){
                inPath.val(
                    inPath.attr('placeholder')
                    + e.target.value
                )
            })
        }
    })
</script>
@endpush