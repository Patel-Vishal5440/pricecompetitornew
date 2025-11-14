@extends('layouts.app')
@section('content')
<div class="contents">
   <div class="container-fluid">
      <div class="social-dash-wrap">
         <div class="row">
            <div class="col-lg-12">
               <div class="breadcrumb-main">
                  <h4 class="text-capitalize breadcrumb-title">Export Selected</h4>
                  <div class="breadcrumb-action justify-content-center flex-wrap">
                     <div class="action-btn">
                        <div class="form-group mb-0">
                           <div class="input-container icon-left position-relative">
                              <span class="input-icon icon-left">
                              <span data-feather="calendar"></span>
                              </span>
                              <input type="text" class="form-control form-control-default date-ranger" name="date-ranger" placeholder="Oct 30, 2019 - Nov 30, 2019">
                              <span class="input-icon icon-right">
                              <span data-feather="chevron-down"></span>
                              </span>
                           </div>
                        </div>
                     </div>
                     <div class="dropdown action-btn">
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="la la-download"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                           <span class="dropdown-item">Export With</span>
                           <div class="dropdown-divider"></div>
                           <a href="" class="dropdown-item">
                           <i class="la la-print"></i> Printer</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-file-pdf"></i> PDF</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-file-text"></i> Google Sheets</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-file-excel"></i> Excel (XLSX)</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-file-csv"></i> CSV</a>
                        </div>
                     </div>
                     <div class="dropdown action-btn">
                        <button class="btn btn-sm btn-default btn-white dropdown-toggle" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="la la-share"></i> Share
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu3">
                           <span class="dropdown-item">Share Link</span>
                           <div class="dropdown-divider"></div>
                           <a href="" class="dropdown-item">
                           <i class="la la-facebook"></i> Facebook</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-twitter"></i> Twitter</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-google"></i> Google</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-feed"></i> Feed</a>
                           <a href="" class="dropdown-item">
                           <i class="la la-instagram"></i> Instagram</a>
                        </div>
                     </div>
                     <div class="action-btn">
                        <a href="" class="btn btn-sm btn-primary btn-add">
                        <i class="la la-plus"></i> Add New</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-lg-12">
               <div class="exportDatatable userDatatable orderDatatable global-shadow border py-30 bg-white radius-xl w-100 mb-30">
                  <div class="project-top-wrapper d-flex justify-content-between flex-wrap mb-25 mt-n10 px-sm-30 px-20 ">
                     <div class="content-center mt-10">
                        <div class="button-group m-0 mt-xl-0 mt-sm-10 order-button-group ">
                            <button type="button" class="btn-primary btn radius-md" data-toggle="modal" data-target="#staticBackdrop5">Export Selected</button>
                           <!-- Modal -->
                           <div class="modal fade fileM-Modal-overlay" id="staticBackdrop5" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdrop5Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-500" id="staticBackdrop5Label">Export Selected File</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" data-feather="x"></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="form-group mb-15">
                                                    <input type="text" class="form-control" placeholder="Enter File Name">
                                                </div>
                                                <div class="mb-25">
                                                    <select class="js-example-basic-single js-states form-control" id="countryOption">
                                                        <option value="JAN">CSV</option>
                                                        <option value="FBR">XML</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer m-n1">
                                            <button type="button" class="btn btn-light btn-default btn-squared fw-400 text-capitalize b-light text-light" data-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary btn-default btn-squared text-capitalize">Create</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                     <!-- End: .content-center -->
                     <div class="d-flex align-items-center flex-wrap justify-content-center">
                        <div class="project-search order-search  global-shadow mt-10">
                            <form action="/" class="order-search__form">
                                <span data-feather="search"></span>
                                <input class="form-control mr-sm-2 border-0 box-shadow-none" type="search" placeholder="Search" aria-label="Search">
                            </form>
                        </div>
                        <!-- End: .project-search -->
                        <!-- End: .project-category -->
                     </div>
                     <!-- End: .d-flex -->
                  </div>
                  <!-- End: .project-top-wrapper -->
                  <div class="table-responsive">
                    <table class="table mb-0 table-hover table-borderless border-0 px-30 pb-30">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>
                                    <div class="d-flex align-items-center">

                                        <div class="bd-example-indeterminate">
                                            <div class="checkbox-theme-default custom-checkbox  check-all">
                                                <input class="checkbox" type="checkbox" id="check-23e">
                                                <label for="check-23e">
                                                    <span class="checkbox-text ml-3">
                                                        User
                                                    </span>
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Email</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Company</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title">Position</span>
                                </th>
                                <th>
                                    <span class="userDatatable-title float-right">Join Date</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 d-flex align-items-center">
                                            <div class="checkbox-group-wrapper">
                                                <div class="checkbox-group d-flex">
                                                    <div class="checkbox-theme-default custom-checkbox checkbox-group__single d-flex">
                                                        <input class="checkbox" type="checkbox" id="check-grp-12">
                                                        <label for="check-grp-12"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="orderDatatable-title">
                                            <p class="d-block mb-0">
                                                Kellie Marquot
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        john-keller@gmail.com
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Business Development
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Web Developer
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title float-right">
                                        January 20, 2020
                                    </div>
                                </td>
                            </tr>
                            <!-- End: tr -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 d-flex align-items-center">
                                            <div class="checkbox-group-wrapper">
                                                <div class="checkbox-group d-flex">
                                                    <div class="checkbox-theme-default custom-checkbox checkbox-group__single d-flex">
                                                        <input class="checkbox" type="checkbox" id="check-grp-13">
                                                        <label for="check-grp-13"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="orderDatatable-title">
                                            <p class="d-block mb-0">
                                                Kellie Marquot
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        john-keller@gmail.com
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Business Development
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Web Developer
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title float-right">
                                        January 28, 2020
                                    </div>
                                </td>
                            </tr>
                            <!-- End: tr -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 d-flex align-items-center">
                                            <div class="checkbox-group-wrapper">
                                                <div class="checkbox-group d-flex">
                                                    <div class="checkbox-theme-default custom-checkbox checkbox-group__single d-flex">
                                                        <input class="checkbox" type="checkbox" id="check-grp-14">
                                                        <label for="check-grp-14"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="orderDatatable-title">
                                            <p class="d-block mb-0">
                                                Kellie Marquot
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        john-keller@gmail.com
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Business Development
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title">
                                        Web Developer
                                    </div>
                                </td>
                                <td>
                                    <div class="orderDatatable-title float-right">
                                        January 28, 2020
                                    </div>
                                </td>
                            </tr>
                            <!-- End: tr -->
                        </tbody>
                    </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection