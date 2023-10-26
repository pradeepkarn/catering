<?php
$user_detail = $context->user_detail;
$ud = obj($user_detail);
$ug =  explode("/", REQUEST_URI);
$ug = $ug[3];
$req = new stdClass;
$req->ug = $ug;
?>

<form action="/<?php echo home . route('userUpdateAjax', ['id' => $ud->id, 'ug' => $req->ug]); ?>" id="update-new-user-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add user</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('userList', ['ug' => $req->ug]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Email</h4>
                            <input type="email" value="<?php echo $ud->email; ?>" name="email" class="form-control my-3" placeholder="Eemail">
                        </div>
                        <div class="col-md-4">
                            <h4>Username</h4>
                            <input type="text" name="username" value="<?php echo $ud->username; ?>" class="form-control my-3" placeholder="username">
                        </div>
                        <div class="col-md-6">
                            <h4>First name</h4>
                            <input type="text" name="first_name" value="<?php echo $ud->first_name; ?>" class="form-control my-3" placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <h4>Lats name</h4>
                            <input type="text" name="last_name" value="<?php echo $ud->last_name; ?>" class="form-control my-3" placeholder="Last name">
                        </div>
                        <?php if ($req->ug == 'caterer') : ?>

                            <div class="col-md-6 my-2">
                                <label for="">National ID No.</label>
                                <input type="text" name="nid_no" value="<?php echo $ud->nid_no; ?>" class="form-control">
                            </div>
                            <div class="col-md-6 my-2">
                                <label for="">National ID DOC (PDF)</label>
                                <input accept="application/pdf" type="file" name="nid_doc" class="form-control">
                            </div>


                            <!-- <div class="col-md-6 my-2">
                                <label for="">Vehicle No.</label>
                                <input type="text" name="vhcl_no" value="<?php //echo $ud->vhcl_no; 
                                                                            ?>" class="form-control">
                            </div>
                            <div class="col-md-6 my-2">
                                <label for="">Vehicle Doc (PDF)</label>
                                <input accept="application/pdf" type="file" name="vhcl_doc" class="form-control">
                            </div>

                            <div class="col-md-6 my-2">
                                <label for="">DL No.</label>
                                <input type="text" name="dl_no" value="<?php //echo $ud->dl_no; 
                                                                        ?>" class="form-control">
                            </div>
                            <div class="col-md-6 my-2">
                                <label for="">DL DOC (PDF)</label>
                                <input accept="application/pdf" type="file" name="dl_doc" class="form-control">
                            </div> -->

                        <?php endif; ?>
                        <div class="col-md-12">
                            <h4>Bio</h4>
                            <textarea class="form-control" name="bio" aria-hidden="true"><?php echo $ud->bio; ?></textarea>
                            <div class=" my-2 text-center">
                                <a download href="/<?php echo MEDIA_URL . "/images/qrcodes/" . $ud->email; ?>.png">
                                    <img style="border: 2px dotted green; height: 200px; width:200px; object-fit:contain;" src="/<?php echo home . route('generateQRCode', ['id' => $ud->id]); ?>" alt="">
                                </a>
                                <p>Click QR Code to download</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h4>Profile Image</h4>
                    <input accept="image/*" id="image-input" type="file" name="image" class="form-control my-3">
                    <div class="text-center">
                        <img style="width:200px; height:200px; object-fit:cover; border-radius:50%;" id="image" src="/<?php echo MEDIA_URL; ?>/images/profiles/<?php echo $ud->image; ?>" alt="<?php echo $ud->image; ?>">
                    </div>

                    <h4>Password</h4>
                    <input type="text" name="password" class="form-control my-3" placeholder="Password">

                    <div class="d-grid">
                        <button id="update-user-btn" type="button" class="btn btn-primary my-3">Update</button>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">

                        <a class="btn btn-sm btn-success" target="_blank" href="/<?php echo MEDIA_URL . "/docs/" . $ud->nid_doc; ?>"> <i class="bi bi-eye"></i> National ID</a>


                    </div>


                </div>
            </div>

        </div>
    </div>

</form>
<script>
    const imageInputPost = document.getElementById('image-input');
    const imagePost = document.getElementById('image');

    imageInputPost.addEventListener('change', (event) => {
        const file = event.target.files[0];
        const fileReader = new FileReader();

        fileReader.onload = () => {
            imagePost.src = fileReader.result;
        };

        fileReader.readAsDataURL(file);
    });
</script>
<?php pkAjax_form("#update-user-btn", "#update-new-user-form", "#res"); ?>