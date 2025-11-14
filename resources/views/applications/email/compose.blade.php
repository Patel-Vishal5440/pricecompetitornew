<div class="col-lg-2">
<aside class="mailbox-sidebar-container">
    <div class="atbd-mail-sidebar show mb-30">
        <div class="card">
            <a href="#" class="mailbar-cross d-md-none">
                <span data-feather="x"></span>
            </a>
            <div class="card-body">
                <div class="d-flex align-content-center content-center px-15">
                    <a href="#" class="btn-compose btn btn-md btn-primary btn-shadow-primary"
                        data-trigger="compose"> <span data-feather="plus"></span> Compose</a>
                </div>
                <ul class="mail-list">
                    <li><a class="{{ Route::is('applications.email')  ? 'active' : ''}}" href="{{ route('applications.email') }}"><span data-feather="inbox"></span>Inbox <span
                                class="badge badge-primary badge-transparent">9</span></a></li>
                    <li><a class="{{ Route::is('applications.started')  ? 'active' : ''}}" href="{{ route('applications.started') }}"><span data-feather="star"></span>Started</a></li>
                    <li><a class="{{ Route::is('applications.send')  ? 'active' : ''}}" href="{{ route('applications.send') }}"><span data-feather="send"></span>Send</a></li>
                    <li><a href="#"><span data-feather="edit"></span>Draft <span
                                class="badge badge-primary badge-transparent">12</span></a></li>
                    <li><a href="#"><span data-feather="alert-octagon"></span>Spam</a></li>
                    <li><a href="#"><span data-feather="trash-2"></span>Trash</a></li>
                </ul>
                <span class="mail-list-title m-top-35">Lable</span>
                <ul class="mail-list mt-0">
                    <li><a href="#"><span data-feather="list"></span>Personal</a></li>
                    <li><a href="#"><span data-feather="list"></span>Social</a></li>
                    <li><a href="#"><span data-feather="list"></span>Promotion</a></li>
                </ul>
                <div class="btn-add-label" data-trigger="label">
                    <span class="cursor-true"><span data-feather="plus"></span>Add New
                        Label</span>
                    <div class="add-lebel-from">
                        <form action="#">
                            <h6>Add New Label</h6>
                            <input type="text" class="form-control" name="label"
                                Placeholder="Enter label name">
                            <div class="label-action d-flex">
                                <button class="btn btn-primary btn-sm btn-squared">Add
                                    Label</button>
                                <button class="btn btn-white btn-sm btn-squared label-close"
                                    data-trigger="label">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- ends: .card -->
    </div><!-- ends: .atbd-mail-sidebar -->
</aside>
</div>