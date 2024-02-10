@extends('Widget.modal')
@section('modal-id', 'new-object')
@section('modal-title', 'New File/Directory')

@section('modal-body')
@csrf
<div class="row">
  <div class="col-5 col-sm-3">
    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link text-light active" id="vert-new-file" data-toggle="pill" href="#vert-tab-new-file" role="tab" aria-controls="vert-tab-new-file" aria-selected="true">
        New File
      </a>
      <a class="nav-link text-light" id="vert-new-directory" data-toggle="pill" href="#vert-tab-directory" role="tab" aria-controls="vert-tab-directory" aria-selected="false">
        New Directory
      </a>
      <a class="nav-link text-light" id="vert-remote-download" data-toggle="pill" href="#vert-tab-remote-download" role="tab" aria-controls="vert-tab-remote-download" aria-selected="false">
        Remote Download
      </a>
      <a class="nav-link text-light" id="vert-upload-file" data-toggle="pill" href="#vert-tab-upload-file" role="tab" aria-controls="vert-tab-upload-file" aria-selected="false">
        Upload File
      </a>
    </div>
  </div>
  <div class="col-7 col-sm-9">
    <div class="tab-content" id="vert-tabs-tabContent">
      <div class="tab-pane text-left fade show active" id="vert-tab-new-file" role="tabpanel" aria-labelledby="vert-new-file">
        <div class="mb-3">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper dui molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam odio magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi, vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta, ante et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta sem. Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras lacinia erat eget sapien porta consectetur.
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
      <div class="tab-pane fade" id="vert-tab-directory" role="tabpanel" aria-labelledby="vert-new-directory">
        <div class="mb-3">
          Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
      <div class="tab-pane fade" id="vert-tab-remote-download" role="tabpanel" aria-labelledby="vert-remote-download">
        <div class="mb-3">
          Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
      <div class="tab-pane fade" id="vert-tab-upload-file" role="tabpanel" aria-labelledby="vert-upload-file">
        <div class="mb-3">
          Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </div>
    </div>
  </div>
</div>
@endsection