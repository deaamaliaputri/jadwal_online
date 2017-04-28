app.controller('StudentsCreateCtrl', ['$state', '$scope', 'students','$timeout', 'SweetAlert','toaster','$http', function ($state, $scope, students,$timeout, SweetAlert,toaster) {
       $scope.myModel ={}
 
    //Init input addForm variable
    //create students
    $scope.process = false;

    $scope.master = $scope.myModel;
    $scope.form = {

        submit: function (form) {
            var firstError = null;
            if (form.$invalid) {

                var field = null, firstError = null;
                for (field in form) {
                    if (field[0] != '$') {
                        if (firstError === null && !form[field].$valid) {
                            firstError = form[field].$name;
                        }

                        if (form[field].$pristine) {
                            form[field].$dirty = true;
                        }
                    }
                }
                angular.element('.ng-invalid[name=' + firstError + ']').focus();
                SweetAlert.swal("The form cannot be submitted because it contains validation errors!", "Errors are marked with a red, dashed border!", "error");
                return;

            } else {
                SweetAlert.swal("Good job!", "Your form is ready to be submitted!", "success");
                //your code for submit
            }

        },
        reset: function (form) {

            $scope.myModel = angular.copy($scope.master);
            form.$setPristine(true);
        }

    };
             $scope.objDepartments =[]
students.getListdepartment()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                        $scope.toaster = {
                        type: 'warning',
                        title: 'Warning',
                        text: 'Data Belum Tersedia!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({id: 0, name: 'Silahkan Pilih Jurusan'});
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

        $scope.objKelas =[]
students.getListkelas()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                        $scope.toaster = {
                        type: 'warning',
                        title: 'Warning',
                        text: 'Data Belum Tersedia!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({id: 0, name: 'Silahkan Pilih Kelas'});
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

    $scope.closeAlert = function (index) {
        $scope.alerts.splice(index, 1);
    };
    $scope.clearInput = function () {
        $scope.myModel.name = null;
        $scope.myModel.nis= null;
        $scope.myModel.kelas= null;
        $scope.myModel.jurusan= null;
    };

    $scope.submitData = function (isBack) {
        $scope.alerts = [];
        //Set process status
        $scope.process = true;
        //Close Alert

        //Check validation status
        if ($scope.Form.$valid) {
            //run Ajax
            $scope.myModel.kelas_id=  $scope.myModel.kelas.id
            $scope.myModel.departments_id=  $scope.myModel.departments.id
            students.store($scope.myModel)
                .success(function (data) {
                    if (data.created == true) {
                        //If back to list after submitting
                        if (isBack == true) {
                            $state.go('app.students');
                            $scope.toaster = {
                                type: 'success',
                                title: 'Sukses',
                                text: 'Simpan Data Berhasil!'
                            };
                                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                        } else {
                            $scope.sup();
                            $scope.alerts.push({
                                type: 'success',
                                msg: 'Simpan Data Berhasil!'
                            });
                            $scope.process = false;
                            $scope.toaster = {
                                type: 'success',
                                title: 'Sukses',
                                text: 'Simpan Data Berhasil!'
                            };
                            toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                        }
                        //Clear Input
                    } else {
                        $scope.process = false;
                        //$scope.alertset.class = 'orange';
                        $scope.toaster = {
                            type: 'success',
                            title: 'Sukses',
                            text: 'Simpan Data Berhasil!'
                        };
                        toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                        $scope.clearInput();

                        //Set Alert message
                        $scope.sup();
                        $scope.alerts.push({
                            type: 'success',
                            msg: 'Simpan Data Berhasil!'
                        });

                    }

                })
                .error(function (data, status) {
                    // unauthorized
                    if (status === 401) {
                        //redirect to login
                        $scope.redirect();
                    }
                    $scope.sup();
                    // Stop Loading
                    $scope.process = false;
                    $scope.alerts.push({
                        type: 'danger',
                        msg: data.validation
                    });
                    $scope.toaster = {
                        type: 'error',
                        title: 'Gagal',
                        text: 'Simpan Data Gagal!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                });
        }
    };

}]);
