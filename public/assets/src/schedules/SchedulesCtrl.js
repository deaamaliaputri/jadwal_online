'use strict';

app.controller('SchedulesCtrl', ['$scope', 'schedules', 'SweetAlert', '$uibModal','$log','$http','$timeout', function ($scope, schedules,SweetAlert,$uibModal,$log) {
//urussan tampilan
    $scope.main = {
        page: 1,
        term: ''
    };

    $scope.open = function (size) {

        var modalInstance = $uibModal.open({
            templateUrl: 'assets/src/schedules/print.html',
            controller: 'SchedulesdetailCtrl',
            size: size,
            resolve: {
                items: function () {
                    return $scope.items;
                }
            }
        });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });
    };


    $scope.isLoading = true;
    $scope.isLoaded = false;

    $scope.setLoader = function (status) {
        if (status == true) {
            $scope.isLoading = true;
            $scope.isLoaded = false;
        } else {
            $scope.isLoading = false;
            $scope.isLoaded = true;
        }
    };

    //Init Alert status
    $scope.alertset = {
        show: 'hide',
        class: 'green',
        msg: ''
    };
    //refreshData
    $scope.refreshData = function () {
        $scope.main.page = 1;
        $scope.main.term = '';
        $scope.getData();
    };
    // go to print preview page
    $scope.print = function () {
        window.open ('../api/v1/cetak-schedules','_blank');
    };
    //Init dataAkun
    $scope.dataschedules = '';
    // init get data
    schedules.get($scope.main.page, $scope.main.term)
        .success(function (data) {

            //Change Loading status
            $scope.setLoader(false);

            // result data
            $scope.dataschedules = data.data;
            // set the current page
            $scope.current_page = data.current_page;

            // set the last page
            $scope.last_page = data.last_page;

            // set the data from
            $scope.from = data.from;

            // set the data until to
            $scope.to = data.to;

            // set the total result data
            $scope.total = data.total;
        })
        .error(function (data, status) {
            // unauthorized
            if (status === 401) {
                //redirect to login
                $scope.redirect();
            }
            console.log(data);
        });

    // get data
    $scope.getData = function () {

        //Start loading
        $scope.setLoader(true);

        schedules.get($scope.main.page, $scope.main.term)
            .success(function (data) {

                //Stop loading
                $scope.setLoader(false);

                // result data
                $scope.dataschedules = data.data;

                // set the current page
                $scope.current_page = data.current_page;

                // set the last page
                $scope.last_page = data.last_page;

                // set the data from
                $scope.from = data.from;

                // set the data until to
                $scope.to = data.to;

                // set the total result data
                $scope.total = data.total;
            })
            .error(function (data, status) {
                // unauthorized
                if (status === 401) {
                    //redirect to login
                    $scope.redirect();
                }
                console.log(data);
            });
    };

    // Navigasi halaman terakhir
    $scope.lastPage = function () {
        //Disable All Controller
        $scope.main.page = $scope.last_page;
        $scope.getData();
    };

    // Navigasi halaman selanjutnya
    $scope.nextPage = function () {
        // jika page = 1 < halaman terakhir
        if ($scope.main.page < $scope.last_page) {
            // halaman saat ini ditambah increment++
            $scope.main.page++
        }
        // panggil ajax data
        $scope.getData();
    };

    // Navigasi halaman sebelumnya
    $scope.previousPage = function () {
        //Disable All Controller

        // jika page = 1 > 1
        if ($scope.main.page > 1) {
            // page dikurangi decrement --
            $scope.main.page--
        }
        // panggil ajax data
        $scope.getData();
    };

    // Navigasi halaman pertama
    $scope.firstPage = function () {
        //Disable All Controller

        $scope.main.page = 1;

        $scope.getData()
    };

  $scope.hapus = function (id) {
        SweetAlert.swal({
            title: "Peringatan?",
            text: "Apakah anda yakin ingin hapus",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete!",
            cancelButtonText: "Batal!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function (isConfirm) {
            if (isConfirm) {
                schedules.destroy(id)
                    .success(function (data) {
                        if (data.deleted == true) {
                            SweetAlert.swal({
                                title: "Berhasil!",
                                text: "Data Berhasil Dihapus.",
                                type: "success",
                                confirmButtonColor: "#007AFF"
                            });

                        } else {
                            SweetAlert.swal({
                                title: "Gagal",
                                text: "Data Gagal Dihapus :)",
                                type: "error",
                                confirmButtonColor: "#007AFF"
                            })

                        }
                        $scope.getData();
                    })


            } else {
                SweetAlert.swal({
                    title: "Cancelled",
                    text: "Your imaginary file is safe :)",
                    type: "error",
                    confirmButtonColor: "#007AFF"
                });
            }
        });
    };

}]);
app.controller('SchedulesdetailCtrl', ['$scope', 'schedules', 'SweetAlert', '$uibModal','$log','toaster','$http','$timeout', function ($scope, schedules,SweetAlert,$uibModal,$log,toaster) {
//urussan tampilan
    $scope.myModel ={}


    $scope.isLoading = true;
    $scope.isLoaded = false;

    $scope.setLoader = function (status) {
        if (status == true) {
            $scope.isLoading = true;
            $scope.isLoaded = false;
        } else {
            $scope.isLoading = false;
            $scope.isLoaded = true;
        }
    };


    $scope.objDepartments = []
    schedules.getListdepartment()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                $scope.toaster = {
                    type: 'warning',
                    title: 'Warning',
                    text: 'Data Belum Tersedia!'
                };
                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({ id: 0, name: 'Silahkan Pilih Jurusan' });
                $scope.objDepartments = data_akun;
                $scope.myModel.departments = $scope.objDepartments[0];
            }

        })
        .error(function (data_akun, status) {
            // unauthorized
            if (status === 401) {
                //redirect to login
                $scope.redirect();
            }
            // Stop Loading
            $scope.toaster = {
                type: 'warning',
                title: 'Warning',
                text: 'Data Belum Tersedia!'
            };
            toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            console.log(data_akun);

        });

    $scope.objKelas = []
    schedules.getListkelas()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                $scope.toaster = {
                    type: 'warning',
                    title: 'Warning',
                    text: 'Data Belum Tersedia!'
                };
                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({ id: 0, name: 'Silahkan Pilih Kelas' });
                $scope.objKelas = data_akun;
                $scope.myModel.kelas = $scope.objKelas[0];
            }

        })
        .error(function (data_akun, status) {
            // unauthorized
            if (status === 401) {
                //redirect to login
                $scope.redirect();
            }
            // Stop Loading
            $scope.toaster = {
                type: 'warning',
                title: 'Warning',
                text: 'Data Belum Tersedia!'
            };
            toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            console.log(data_akun);

        });



    //Init Alert status
    $scope.alertset = {
        show: 'hide',
        class: 'green',
        msg: ''
    };
    //refreshData
    // go to print preview page
    $scope.ok = function () {
        schedules.cekcetak($scope.myModel.kelas.id,$scope.myModel.departments.id)
            .success(function (data) {
                if (data.success == true) {

                    window.open('../api/cetak-daftar/'+$scope.myModel.kelas.id+'/'+$scope.myModel.departments.id, '_blank');
                }
                else{
                    $scope.toaster = {
                        type: 'error',
                        title: 'Cek Data Anda',
                        text:  data.result
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

                }
            })
    };

}]);
