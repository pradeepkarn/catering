<?php
$createData = $context;
$catlist = $context->cat_list;
$user_list = $context->user_list;
?>

<form action="/<?php echo home . route('eventStoreAjax'); ?>" id="save-new-page-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add event</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('eventList'); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <h4>Title</h4>
                    <input type="text" name="title" class="form-control my-3" placeholder="Title">
                    <h6>Slug</h6>
                    <input type="text" name="slug" class="form-control my-3" placeholder="slug">

                    

                    <!-- <h4>Category</h4>
                    <select name="parent_id" class="form-select my-3">
                        <option value="0">Uncategorised</option>
                        <?php // foreach ($catlist as  $cv) {
                        //$cv = obj($cv);
                        ?>
                            <option value="<?php // echo $cv->id; 
                                            ?>"><?php // echo $cv->title; 
                                                ?></option>
                        <?php //} 
                        ?>
                        <?php ?>
                    </select> -->
                    <!-- <div class="row">
                        <div class="col">
                            <label for="">Latitude</label>
                            <input type="text" class="form-control my-2" name="lat">
                        </div>
                        <div class="col">
                            <label for="">Longitude</label>
                            <input type="text" class="form-control my-2" name="lon">
                        </div>
                    </div> -->
                    <textarea class="tinymce-editor" name="content" id="mce_0" aria-hidden="true"></textarea>
                    <h4>Tags</h4>
                    <textarea class="form-control" name="meta_tags" aria-hidden="true"></textarea>
                    <h4>Meta description</h4>
                    <textarea class="form-control" name="meta_description" aria-hidden="true"></textarea>
                </div>
                <div class="col-md-4">
                    <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="" alt="">
                    <div id="image-container"></div>
                    <button type="button" class="btn btn-secondary text-white mt-2" id="add-image">Images <i class="bi bi-plus"></i> </button>
                    <hr>
                    <div class="dropdown my-3 d-grid">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="employeeDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            Select Managers
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="employeeDropdown">
                            <?php foreach ($user_list as $key => $emp) :
                                $emp = obj($emp);
                                ?>
                                <li class="px-2">
                                    <div class="form-group">
                                        <label for="employee1">
                                            <input name="managers[]" type="checkbox" value="<?php echo $emp->id; ?>" class="form-check-input" id="employee1"> <?php echo $emp->first_name; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="dropdown my-3 d-grid">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="employeeDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            Select Employees
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="employeeDropdown">
                            <?php foreach ($user_list as $key => $emp) :
                                $emp = obj($emp);
                                ?>
                                <li class="px-2">
                                    <div class="form-group">
                                        <label for="employee1">
                                            <input name="employees[]" type="checkbox" value="<?php echo $emp->id; ?>" class="form-check-input" id="employee1"> <?php echo $emp->first_name; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- <h4>Price/Unit</h4>
                    <input type="number" scope="any" name="price" class="form-control my-3" placeholder="Price"> -->

                    <!-- <h4>Min. Age</h4>
                    <input type="text" name="min_age" class="form-control my-3" placeholder="Min age"> -->

                    <h4>Event Date</h4>
                    <input type="date" name="event_date" class="form-control my-3">
                    <h4>Start at</h4>
                    <input type="time" name="event_time" class="form-control my-3">

                    <h4>Address</h4>
                    <textarea name="address" class="form-control my-3"></textarea>

                    <!-- <h4>Languages</h4>
                    <input type="text" name="languages" class="form-control my-3" placeholder="Hindi, Arabic, English, Spanish"> -->

                    <!-- <h4>No. of days for tours</h4>
                    <input type="number" scope="any" name="days" class="form-control my-3" placeholder="Days for tours"> -->

                    <h4>City</h4>
                    <input type="text" name="city" class="form-control my-3" placeholder="City">

                    <div class="d-grid">
                        <button id="save-page-btn" type="button" class="btn btn-primary my-3">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
    window.onload = () => {
        const imageInputPage = document.getElementById('image-input');
        const imagePage = document.getElementById('banner');

        imageInputPage.addEventListener('change', (event) => {
            const file = event.target.files[0];
            const fileReader = new FileReader();

            fileReader.onload = () => {
                imagePage.src = fileReader.result;
            };

            fileReader.readAsDataURL(file);
        });

        // for slug

        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', () => {
                const title = titleInput.value.trim();
                generateSlug(title, slugInput);
            });
        }
    }



    $(document).ready(function() {
        $('#add-image').on('click', function() {
            // Create a new image input field
            var newInput = '<input accept="image/*" type="file" name="moreimgs[]" class="form-control my-3">';
            $('#image-container').append(newInput);
        });
    });
</script>
<?php pkAjax_form("#save-page-btn", "#save-new-page-form", "#res"); ?>