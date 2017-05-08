app.controller('SchedulesCreateCtrl', ['$state', '$scope', 'schedules', '$timeout', 'SweetAlert', 'toaster', '$http', function ($state, $scope, schedules, $timeout, SweetAlert, toaster) {
    //Init input addForm variable
    //create schedules
    $scope.myModel = {}
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
$scope.getlist = function () {
    $scope.objsubjects = []
    schedules.getListsubjects()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                $scope.toaster = {
                    type: 'warning',
                    title: 'Warning',
                    text: 'Data Belum Tersedia!'
                };
                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({ id: 0, name: 'Silahkan Pilih Mata Pelajaran' });
                $scope.objsubjects = data_akun;
                $scope.myModel.subjects = $scope.objsubjects[0];
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
            toaster.pop($scope.toaster.type, $scope.toaster.
                title, $scope.toaster.text);

            console.log(data_akun);

        });

    $scope.objTeachers = []
    schedules.getListteachers()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                $scope.toaster = {
                    type: 'warning',
                    title: 'Warning',
                    text: 'Data Belum Tersedia!'
                };
                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({ id: 0, name: 'Silahkan Pilih Guru' });
                $scope.objTeachers = data_akun;
                $scope.myModel.teachers = $scope.objTeachers[0];
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

$scope.objWalikelas = []
    schedules.getListteachers()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                $scope.toaster = {
                    type: 'warning',
                    title: 'Warning',
                    text: 'Data Belum Tersedia!'
                };
                toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({ id: 0, name: 'Silahkan Pilih Wali Kelas' });
                $scope.objWalikelas = data_akun;
                $scope.myModel.wali_kelas = $scope.objWalikelas[0];
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

}

    $scope.closeAlert = function (index) {
        $scope.alerts.splice(index, 1);
    };
    $scope.getlist()
    $scope.clearInput = function () {
        $scope.myModel.time = null;
        $scope.myModel.hour = null;
        $scope.myModel.room = null;
        $scope.myModel.subjects = null;
        $scope.myModel.teachers = null;
        $scope.myModel.departments = null;
        $scope.myModel.kelas = null;
        $scope.myModel.hari = null;
        $scope.myModel.wali_kelas = null;
        $scope.getlist()
    };

    $scope.submitData = function (isBack) {
        $scope.alerts = [];
        //Set process status
        $scope.process = true;
        //Close Alert

        //Check validation status
        if ($scope.Form.$valid) {
            //run Ajax
            $scope.myModel.kelas_id = $scope.myModel.kelas.id
            $scope.myModel.teachers_id = $scope.myModel.teachers.id
            $scope.myModel.subjects_id = $scope.myModel.subjects.id
            $scope.myModel.departments_id = $scope.myModel.departments.id
            $scope.myModel.wali_kelas= $scope.myModel.wali_kelas.id

            schedules.store($scope.myModel)
                .success(function (data) {
                    if (data.created == true) {
                        //If back to list after submitting

                        if (isBack == true) {
                            $state.go('app.schedules');
                            $scope.toaster = {
                                type: 'success',
                                title: 'Sukses',
                                text: 'Simpan Data Berhasil!'
                            };
                            toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                        } else {
                     $scope.clearInput();
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
                        // $scope.clearInput();

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
