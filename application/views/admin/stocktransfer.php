<div class="col-md-12">
    <?php
    defined('BASEPATH') or exit('No direct script access allowed');
    if (isset($data)) {
        if (!empty($data)) {
            $formData = $data;
        } else {
            $formData = $this->form_validation->get_session_data();
        }
    } else {
        $formData = $this->form_validation->get_session_data();
    }
    ?>
    <?php if (isset($form_action)) { ?>
        <div class="">
            <div class="main-card mb-3 card card-body">
                <h5 class="card-title"></h5>
                <?php
                if (isset($formData['id'])) {
                    $id = $formData['id'];
                    $url = CONFIG_SERVER_ADMIN_ROOT . "StockTransfer/edit/$id";
                } else {
                    $url = CONFIG_SERVER_ADMIN_ROOT . "StockTransfer/add";
                }
                echo form_open($url, array('class' => 'StockTransfer', 'id' => 'StockTransfer')); ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first name" class="">Source Warehouse <span class="text-danger">*</span></label>
                            <select name="source_warehouse_id" id="source_warehouse_id" class="warehouse form-control">
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first name" class="">Destination Warehouse <span class="text-danger">*</span></label>
                            <select name="destination_warehouse_id" id="destination_warehouse_id" class="warehouse form-control">
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first name" class="">Product<span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">Select Product</option>
                                <?php if(!empty($products)){
                                    foreach($products as $product){    
                                ?>
                                <option value="<?= $product['id'];?>"><?= $product['product_name'];?></option>
                                <?php } } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first name" class="">Available Quantity<span class="text-danger">*</span></label>
                            <input value="" name="available_quantity" readonly ="available_quantity" placeholder="Please Available Quantity" autocomplete='off' type="text" class="available_quantity form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="first name" class="">Transfer Quantity<span class="text-danger">*</span></label>
                            <input value="" name="quantity" id="quantity" placeholder="Please Enter Quantity" autocomplete='off' class="form-control">
                        </div>
                    </div>
                </div>
                <div>
                    <?php if (isset($formData['id'])) { ?>
                        <input type='hidden' name="id" value="<?php echo $formData['id'] ?>">
                        <input type="submit" name="edit" class="mt-2 btn btn-primary pull-right" value="Update">
                    <?php } else { ?>
                        <input type="submit" name="add" class="mt-2 btn btn-primary pull-right" value="Submit">
                    <?php } ?>

                </div>
                </form>
            </div>
        </div>

<script>
getWarehouses();
function getWarehouses(){
    $.ajax({
        url: '<?php echo base_url(); ?>administrator/warehouses/getWarehouses',
        type: 'POST',
        data: {
            "type": type,
        },
        success: function(data) {
            result = JSON.parse(data);
            if (result.error == '0') {
                $("#source_warehouse_id").html(result.html);
                $("#destination_warehouse_id").html(result.html);
            }
        },
        error: function(e) {
            toastr['warning'](e.message);
            // $('#attendanceList').DataTable().ajax.reload();
        }
    });
}
$("#source_warehouse_id,#destination_warehouse_id").change(function(){
    var destination_warehouse_id = $("#destination_warehouse_id").val();
    var source_warehouse_id = $("#source_warehouse_id").val();
    if(source_warehouse_id == destination_warehouse_id){
        toastr['warning']("Both warehouses shouldn't be same");
        $(".warehouse").val('');
    }else{
        return true;
    }
});
$("#product_id").change(function(){
    var product_id = $(this).val();
    var source_warehouse_id = $("#source_warehouse_id").val();
    $.ajax({
        url: '<?php echo base_url(); ?>administrator/inventory/getAvailableStock',
        type: 'POST',
        data: {
            "product_id": product_id,
            "source_warehouse_id":source_warehouse_id
        },
        success: function(res) {
            result = JSON.parse(res);
            if(result.error == 0 && result.data>0){
                $(".available_quantity").val(result.data);
            }else{
                $(".available_quantity").val('');
                toastr['error']('Insufficient Stock to transfer');    
            }
        },
        error: function(e) {
            toastr['error'](e.message);
        }
    });
});
</script>
    <?php } else { ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive tasks dataGridTable">
                            <table id="transferList" class="table card-table table-vcenter text-nowrap mb-0 border nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Source Warehouse</th>
                                        <th>Destionation Warehouse</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Created On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript">
    function getdata() {
        var user_id = $("#user_id").val();
        var clist = $('#transferList').DataTable({
            "destroy": true,
            "responsive": false,
            "dom": 'Bfrtip',
            "processing": true,
            "serverSide": true,
            "order": [
                [0, "desc"]
            ],
            "ajax": {
                "url": "<?php echo CONFIG_SERVER_ADMIN_ROOT ?>StockTransfer/ajaxListing",
                "type": 'POST',
                'data': {
                    user_id:user_id
                }
            },
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function() {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
            }
        });
    }

    getdata();
</script>

<?php } ?>

