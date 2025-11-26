<div class="col-lg-6 col-xl-6 col-12 mb-3 mt-0">
    <div class="input-group input-group-merge">
        <div class="form-floating form-floating-outline">
            <input type="text" id="job-title" name="title" class="form-control" placeholder="Title" aria-label="Finance"
                aria-describedby="basicPost2" required>
            <label for="job-title">Title</label>
        </div>
    </div>
</div>
<div class="col-lg-6 col-xl-6 col-12 mb-3 mt-0 <?php if($space=='active'){echo 'd-block';}else{echo 'd-none';} ?>">
</div>
<div class="col-lg-6 col-xl-6 col-12 mb-3 mt-0">
    <div class="form-floating form-floating-outline">
        <textarea class="form-control h-px-100" name="description" id="exampleFormControlTextarea1"
            placeholder="Write Description Here..." required></textarea>
        <label for="exampleFormControlTextarea1">Description</label>
    </div>
</div>
<div class="col-lg-6 col-xl-6 col-12 mb-3 mt-0">
    <div class="form-floating form-floating-outline">
        <textarea class="form-control h-px-100" name="keywords" id="exampleFormControlTextarea1"
            placeholder="Write Keywords Here..." required></textarea>
        <label for="exampleFormControlTextarea1">Keywords</label>
    </div>
</div>