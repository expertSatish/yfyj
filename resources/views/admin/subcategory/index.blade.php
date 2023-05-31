@include('admin.top-header')

<div class="main-section">

    @include('admin.header')

    <div class="app-content content container-fluid">

        <div class="content-wrapper ">
            <div class="breadcrumbs-top d-flex align-items-center bg-light mb-3">

                <div class="breadcrumb-wrapper">

                    <ol class="breadcrumb bg-transparent mb-0">

                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sub Category</li>
                        </ol>

                </div>



                <div class="add-back ml-auto mr-2">

                    <a href="javascript:void(0)" id="add-category" class="btn text-dark" title="Add Sub Category"><i
                            class="fa fa-plus"></i> Add </a>

                    <a href="javascript:history.go(-1)" class="btn text-dark"> <i class="fa-solid fa-rotate-left"></i>
                        Back</a>

                </div>

            </div>



            <div class="table-responsive">

                <table class="table table-striped table-bordered" id="for_all">

                    <thead>

                        <tr>

                            <th>Date &amp; Time</th>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @if (isset($categories) && count($categories) > 0)

                            @foreach ($categories as $category)
                                <tr>

                                    <td>{{ $category->created_at }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->url }}</td>

                                    <td>

                                        @if ($category->image != null)
                                            <a href="javascript:void(0)" class="view-image"
                                                category_id="{{ $category->id }}">

                                                <img src="{{ URL::asset('storage/categories/' . $category->image) }}"
                                                    height="50" width="50">

                                            </a>
                                        @else
                                            NA
                                        @endif

                                    </td>

                                    <td>{{ $category->status }}</td>

                                    <td class="text-truncate">

                                        <!--<a href="{{ route('admin.manage-sub-category.show', $category->id) }}" class="text-success mr-2" title="Add Subcategory"><i class="fa fa-plus" aria-hidden="true"></i></a>-->

                                        <a href="javascript:void(0)" class="edit-category text-dark mr-2"
                                            category_id="{{ $category->id }}" title="Edit Category"><i
                                                class="fa fa-pencil" aria-hidden="true"></i></a>

                                        <a href="javascript:void(0)" onclick="deleteConfirmation({{ $category->id }})"
                                            class="text-danger" title="Delete Category"><i class="fa fa-trash"
                                                aria-hidden="true"></i></a>

                                    </td>

                                </tr>
                            @endforeach

                        @endif

                    </tbody>

                </table>

                <div class="float-right">
                    {{ $categories->links('pagination::bootstrap-4') }}
                </div>

            </div>

        </div>

    </div>

    <div id="category-modal" class="modal fade" role="dialog">

    </div>

</div>

@include('admin.footer')

<script>
    function deleteConfirmation(id) {

        Swal.fire({

            title: 'Are you sure?',

            text: "You won't be able to revert this!",

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#3085d6',

            cancelButtonColor: '#d33',

            confirmButtonText: 'Yes, delete it!'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: `{{ URL::to('admin/manage-sub-category/${id}') }}`,

                    type: "DELETE",

                    dataType: "json",

                    success: function(result) {

                        if (result.success) {

                            Swal.fire(

                                'Deleted!',

                                'success'

                            );

                            setTimeout(function() {

                                location.reload();

                            }, 400);

                        } else {

                            Swal.fire(result.msgText);

                        }

                    }

                });



            }

        })

    };

    $(document).ready(function(event) {

        $(document).on("click", "#add-category", function(event) {

            $.ajax({

                url: "{{ URL::to('admin/manage-sub-category/create') }}",

                type: "GET",

                dataType: "json",

                success: function(result) {

                    if (result.success) {

                        $("#category-modal").html(result.html);

                        $("#category-modal").modal('show');

                    } else {



                    }

                }

            });

        });



        $(document).on('keyup', "#name", function(event) {

            let name = $(this).val();

            let url = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

            $("#url").val(url);

        })



        $(document).on("keyup", "#meta_title", function(event) {

            let title = $(this).val();

            $('#meta_title-limit').html(
                `We recommend title between 50–60 characters.(${title.length} character)`);

        });



        $(document).on("keyup", "#meta_description", function(event) {

            let title = $(this).val();

            $('#meta_description-limit').html(
                `We recommend descriptions between 50–160 characters.(${title.length} character)`);

        });



        $(document).on("click", "#add-category-btn", function(event) {


            //for (instance in CKEDITOR.instances) {

             //   CKEDITOR.instances[instance].updateElement();

           // }


            $(this).attr('disabled', true);

            //$('#language_id-err').html('');
            //$('#menu-err').html('');
            $('#selected_category-err').html('');
            $('#name-err').html('');
            $('#image-err').html('');
            $('#url-err').html('');
            $('#meta_title-err').html('');
            $('#meta_keyword-err').html('');
            $('#meta_description-err').html('');
            $('#status-err').html('');

            let formData = new FormData();

            //formData.append('language_id', $('#language_id').val());
            //formData.append('menu', $('#menu').val());
            formData.append('selectedcategory', $('#selected_category').val());
            selected_category
            formData.append('name', $('#name').val());
            formData.append('shortdesc_content', $('#shortdesc_content').val());
           // formData.append('aboutus_content', $('#aboutus_content').val());
           // formData.append('second_title', $('#second_title').val());
          //  formData.append('second_content', $('#second_content').val());
            formData.append('url', $('#url').val());
            formData.append('meta_title', $('#meta_title').val());
            formData.append('meta_keyword', $('#meta_keyword').val());
            formData.append('meta_description', $('#meta_description').val());
            formData.append('status', $('#status').val());
            formData.append('image', (typeof $('#image')[0].files[0] == 'undefined') ? '' : $('#image')[
                0].files[0]);
           // formData.append('shortdesc_image', (typeof $('#shortdesc_image')[0].files[0] ==
            //    'undefined') ? '' : $('#shortdesc_image')[0].files[0]);
          //  formData.append('aboutus_image', (typeof $('#aboutus_image')[0].files[0] == 'undefined') ?
              //  '' : $('#aboutus_image')[0].files[0]);

            $.ajax({

                url: "{{ URL::to('admin/manage-sub-category') }}",

                type: 'POST',

                processData: false,

                contentType: false,

                dataType: 'json',

                data: formData,

                context: this,

                success: function(result) {

                    if (result.success) {



                        Swal.fire(

                            'Category!',

                            'Added'

                        );



                        setTimeout(function() {

                            location.reload();

                        }, 400);

                    } else {

                        $(this).attr('disabled', false);

                        if (result.code == 422) {

                            for (const key in result.errors) {

                                $(`#${key}-err`).html(result.errors[key][0]);

                            }

                        } else {

                            console.log(result);

                        }

                    }

                }

            });

        });



        $(document).on("click", ".edit-category", function(event) {

            let id = $(this).attr('category_id');

            $.ajax({

                url: `{{ URL::to('admin/manage-sub-category/${id}/edit') }}`,

                type: "get",

                dataType: "json",

                success: function(result) {

                    if (result.success) {

                        $("#category-modal").html(result.html);

                        $("#category-modal").modal('show');

                    } else {

                        toastr.error('error encountered ' + result.msgText);

                    }

                },

                error: function(error) {

                    toastr.error('error encountered ' + error.statusText);

                }

            });

        });



        $(document).on("click", "#update-category-btn", function(event) {

           // for (instance in CKEDITOR.instances) {

              //  CKEDITOR.instances[instance].updateElement();

           // }


            $(this).attr('disabled', true);
            //$('#language_id-err').html('');
            $('#selected_category-err').html('');
            $('#name-err').html('');
            $('#image-err').html('');
            $('#url-err').html('');
            $('#meta_title-err').html('');
            $('#meta_keyword-err').html('');
            $('#meta_description-err').html('');
            $('#status-err').html('');

            let formData = new FormData();

            formData.append('_method', 'PUT');
            // formData.append('language_id', $('#language_id').val());
            //formData.append('menu', $('#menu').val());
            formData.append('selectedcategory', $('#selected_category').val());


            formData.append('name', $('#name').val());
            formData.append('shortdesc_content', $('#shortdesc_content').val());
           // formData.append('aboutus_content', $('#aboutus_content').val());
           // formData.append('second_title', $('#second_title').val());
           // formData.append('second_content', $('#second_content').val());
            formData.append('url', $('#url').val());
            formData.append('meta_title', $('#meta_title').val());
            formData.append('meta_keyword', $('#meta_keyword').val());
            formData.append('meta_description', $('#meta_description').val());
            formData.append('status', $('#status').val());
            formData.append('image', (typeof $('#image')[0].files[0] == 'undefined') ? '' : $('#image')[
                0].files[0]);
           // formData.append('shortdesc_image', (typeof $('#shortdesc_image')[0].files[0] ==
             //   'undefined') ? '' : $('#shortdesc_image')[0].files[0]);
            //formData.append('aboutus_image', (typeof $('#aboutus_image')[0].files[0] == 'undefined') ?
              //  '' : $('#aboutus_image')[0].files[0]);

            let category_id = $(this).attr('category_id');

            $.ajax({

                url: `{{ URL::to('admin/manage-sub-category/${category_id}') }}`,

                type: 'POST',

                processData: false,

                contentType: false,

                dataType: 'json',

                data: formData,

                context: this,

                success: function(result) {

                    if (result.success) {



                        Swal.fire(

                            'Category!',

                            'Updated'

                        );



                        setTimeout(function() {

                            location.reload();

                        }, 400);

                    } else {

                        $(this).attr('disabled', false);

                        if (result.code == 422) {

                            for (const key in result.errors) {

                                $(`#${key}-err`).html(result.errors[key][0]);

                            }

                        } else {

                            console.log(error);

                        }

                    }

                }

            });

        });

    });
</script>
