@extends('layout')
@section('head', 'Dashboard')

@push('css')
  <link rel="stylesheet" href="{{asset('assets/plugins/uplot/uPlot.min.css')}}">
@endpush

@section('content')
<div class="row">
    <div class="col-6 col-md-4 text-center">
        <div class="card card-body mx-auto">
            <input type="text" class="knob" id="cpu" data-thickness="0.2" data-angleArc="250" data-angleOffset="-125"
                value="0" data-width="120" data-height="120" data-fgColor="#00c0ef" readonly>
            <div class="knob-label">{{$cpus}} CPU (%)</div>
        </div>
    </div>
    <div class="col-6 col-md-4 text-center">
        <div class="card card-body">
            <input type="text" class="knob" id="memory" data-thickness="0.2" data-angleArc="250" data-angleOffset="-125"
                value="0" data-width="120" data-height="120" data-fgColor="#00c0ef" readonly>
            <div class="knob-label">{{$memory->total}} Memory (%)</div>
        </div>
    </div>
    <div class="col-6 col-md-4 text-center">
        <div class="card card-body">
            <input type="text" class="knob" id="storage" data-thickness="0.2" data-angleArc="250" data-angleOffset="-125"
                value="0" data-width="120" data-height="120" data-fgColor="#00c0ef" readonly>
            <div class="knob-label">{{$disk}} Storage (%)</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6 col-md-4 text-center">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-globe"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Site</span>
              <span class="info-box-number">{{$countSite}}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 text-center">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-ethernet"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Traffic</span>
                <span class="info-box-number" id="traffic">0</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 text-center">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-hdd"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Disk I/O</span>
                <span class="info-box-number" id="disk">0</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{asset('assets/plugins/uplot/uPlot.iife.min.js')}}"></script>
<script src="{{asset('assets/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<script>
    $('.knob').knob({
      draw: function () {
        // "tron" case
        if (this.$.data('skin') == 'tron') {
          var a   = this.angle(this.cv),  // Angle            
              sa  = this.startAngle,          // Previous start angle
              sat = this.startAngle,         // Start angle
              ea,                            // Previous end angle
              eat = sat + a,                 // End angle
              r   = true
          this.g.lineWidth = this.lineWidth
          this.o.cursor
          && (sat = eat - 0.3)
          && (eat = eat + 0.3)
          if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value*1)
            this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3)
            this.g.beginPath()
            this.g.strokeStyle = this.previousColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
            this.g.stroke()
          }

          this.g.beginPath()
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
          this.g.stroke()

          this.g.lineWidth = 2
          this.g.beginPath()
          this.g.strokeStyle = this.o.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
          this.g.stroke()

          return false
        }
      }
    })
    
    function serverInfo() {
        $.ajax({
            url: "{{route('server.info')}}",
            type: "post",
            success: function(resp){
                const memory = resp.memory[0]
                const free = Math.round(memory.used/memory.total*10000)/100
                const cpu = resp.cpu
                const storage = resp.storage
                const used = Math.round(storage.used/storage.size*10000)/100
                
                $('#memory').val(free).trigger('change')
                $('#cpu').val(cpu).trigger('change')
                $('#storage').val(used).trigger('change')
            }
        })
    }

    function diskIO() {
        $.ajax({
            url: "{{route('server.diskIO')}}",
            type: "post",
            success: function(resp){
                const read = resp.read
                const write = resp.write

                $('#disk').html(
                    "<sup>"+read+"</sup>" 
                    +" / "+
                    "<sub>"+write+"</sub>"
                )
            }
        })
    }

    let netIn = 0, netOut = 0
    function serverTraffic() {
        $.ajax({
            url: "{{route('server.traffic')}}",
            type: "post",
            success: function(resp){
                const ethIn = Math.round((resp.in.eth0-netIn)/1024*100)/100
                const ethOut = Math.round((resp.out.eth0-netOut)/1024*100)/100
                $('#traffic').html(
                    "<sup>"+ ethIn +" kB</sup>" 
                    +" / "+ 
                    "<sub>"+ ethOut +" kB</sub>"
                )
                netIn = resp.in.eth0
                netOut = resp.out.eth0
            },
        }).then(function(){
            setTimeout(() => {
                diskIO()
                serverInfo()
                serverTraffic()
            }, 5000);
        })
    }

    $('.knob').parent().css('margin', '0 auto')

    document.onreadystatechange = function () {
        if (document.readyState == "complete") {
            diskIO()
            serverInfo()
            serverTraffic()
        }
    }
    
</script>
@endpush