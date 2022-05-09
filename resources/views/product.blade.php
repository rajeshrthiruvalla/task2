@extends('layouts.app')

@section('content')
 <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
 <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" ></script>
 <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js" ></script>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Product Form</div>

                <div class="card-body">
                   <form id="product_form">
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="0">
                        <div class="row">
                        <div class="col-md-8">
                      <label for="name">Name</label>
                      <input type="text" class="form-control" id="name" name="name"  placeholder="Enter Name">
                        </div>
                        <div class="col-md-4">
                      <label for="price">Price</label>
                      <input type="number" step="any" class="form-control" id="price" name="price"  placeholder="Enter Price">
                        </div>
                        </div>
                     </div>
                    <div class="form-group">
                       <label for="description">Description</label>
                       <textarea class="form-control" id="description" name="description"  placeholder="Description"></textarea>
                    </div>
                       <div class="form-group mt-3">
                    <div class="col-md-12">
                        <input type="file" multiple="" id="files" name="files[]" accept="image/png,image/jpeg">
                    </div>
                    <div class="col-md-12" style="min-height: 150px;margin-top: 10px">
                        <div class="row"  id="selected_files">

                        </div>
                        <div class="row"  id="selected_old_files">

                        </div>
                    </div>
                           </div>
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                    <button type="reset" id="reset_data" onclick="resetData()" class="btn btn-info">Reset</button>
                  </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Product List</div>

                <div class="card-body">
           <div class="table-responsive">
                        <table class="table table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th width="150" class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var data_table;
    function resetData()
    {
        data_table.draw();
        $('#id').val(0);
        $('#submit').html('Submit');
           $('#selected_files,#selected_old_files').empty();
    }
    $(document).ready(function(){
        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
        $('#product_form').submit(function(e){
            e.preventDefault();
             var data=new FormData(this);
            var ajax_config={url:'{{route('products.store')}}',
                    type:'post',
                    data:data,
                    dataType:'json',
                    contentType: false, 
                    processData: false,
                    success:function(response){
                        if(response.status)
                        {
                            swal("Success!", "Submitted Successfully!", "success");
                            $('#reset_data').click();
                        }else
                        {
                             swal("Error!", response.error.toString(), "error");
                        }
                    }
            };
            if($('#id').val()>0)
            {
               ajax_config.url='{{url('products')}}/'+$('#id').val();
               data.append('_method','PUT');
            }
            $.ajax(ajax_config)
        });
         data_table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 5,
            // scrollX: true,
            "order": [[ 0, "desc" ]],
            ajax: '{{ route('get_datatable') }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'Actions', name: 'Actions',orderable:false,serachable:false,sClass:'text-center'},
            ]
        });

    })
    function editData(id)
    {
        $('#id').val(0);
        $.ajax({url:'{{url('products')}}/'+id+'/edit',
                    type:'get',
                    dataType:'json',
                    success:function(response){
                        
                        var master=response.master;
                        for(x in master)
                        {
                            $('#'+x).val(master[x]);
                        }
                        $('#submit').html('Update');
                            var details=response.details;
                $('#selected_old_files').empty();
                if(Array.isArray(details))
                {
               details.forEach(function(file,index){
                    var html='<div class="col-md-2"><button class="btn btn-danger btn-sm remove_button" onclick="$(this).closest(\'div\').remove()">X</button>\n\
                               <input type="hidden" value="'+file.name+'" name="old_files[]">\n\
                               <img src="uploads/products/'+file.name+'" style="width:100%">\n\
                              </div>';
                  $('#selected_old_files').append(html);
               });
           }
                    }
            })
    }
    function deleteData(id)
    {
        $.ajax({url:'{{url('products')}}/'+id,
                    type:'delete',
                    dataType:'json',
                    success:function(response){
                        if(response==1)
                        {
                            swal("Success!", "Deleted Successfully!", "success");
                            $('#reset_data').click();
                        }else
                        {
                             swal("Error!", "Try Again", "error");
                        }
                    }
            })
    }
        function fasterPreview( uploader ) {
        $('#selected_files').empty();
       var length=uploader.files.length;
       for(var i=0;i<length;i++)
       {
         var  file=uploader.files[i];
            if (file){
                     var src=window.URL.createObjectURL(file);
                     var html='<div class="col-md-2">'+'<img src="'+src+'" style="width:100%">'+'</div>';
                  $('#selected_files').append(html);
            }
            }
}

$("#files").change(function(){
    fasterPreview( this );
});

</script>
@endsection
