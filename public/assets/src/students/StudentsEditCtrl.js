app.controller('StudentsEditCtrl', ['$state', '$scope', 'students', 'SweetAlert', 'toaster', '$stateParams', function ($state, $scope, students, SweetAlert, toaster, mdToast, $stateParams) {
    $scope.id = $scope.$stateParams.id;
$scope.myModel ={}

    //edit students
    //If Id is empty, then redirected
    if ($scope.id == null || $scope.id == '') {
        $state.go("app.students")
    }

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

    //Init input form variable
    $scope.input = {};

    //Set process status to false
    $scope.process = false;

    //Init Alert status
    $scope.alertset = {
        show: 'hide',
        class: 'green',
        msg: ''
    };
    //get lass students

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

    //Run Ajax
    students.show($scope.id)
        .success(function (data) {
            $scope.setLoader(false);
            $scope.myModel = data;
            students.getListdepartment()
                .success(function (datajk) {
                    datajk.unshift({id: 0, name: 'Silahkan pilih Jurusan'});
                    $scope.objDepartments = datajk;
                    $scope.myModel.departments = $scope.objDepartments[0];
                    $scope.myModel.departments = $scope.objDepartments[findWithAttr($scope.objDepartments, 'id', parseInt(data.departments_id))];
                });
                students.getListkelas()
                .success(function (datajk) {
                    datajk.unshift({id: 0, name: 'Silahkan pilih Kelas'});
                    $scope.objKelas = datajk;
                    $scope.myModel.kelas = $scope.objKelas[0];
                    $scope.myModel.kelas = $scope.objKelas[findWithAttr($scope.objKelas, 'id', parseInt(data.kelas_id))];
                });

        });

    $scope.showToast = function (warna, msg) {
        $mdToast.show({
            //controller: 'AkunToastCtrl',
            template: "<md-toast class='" + warna + "-500'><span flex> " + msg + "</span></md-toast> ",
            //templateUrl: 'views/ui/material/toast.tmpl.html',
            hideDelay: 6000,
            parent: '#toast',
            position: 'top right'
        });
    };
    //Submit Data
    $scope.updateData = function () {
          $scope.alerts = [];
        //Set process status
        $scope.process = true;

        //Close Alert
        // $scope.alertset.show = 'hide';

        //Check validation status
        if ($scope.Form.$valid) {
            //run Ajax
            students.update($scope.myModel)
                .success(function (data) {
                    if (data.updated == true) {
                        //If back to list after submitting
                        //Redirect to akun
                        $state.go('app.students');
                        $scope.toaster = {
                            type: 'success',
                            title: 'Sukses',
                            text: 'Update Data Berhasil!'
                        };
                        toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

                    }
                    else{
                             $scope.alerts.push({
                        type: 'danger',
                        msg: data.validation
                    });
                    $scope.toaster = {
                        type: 'error',
                        title: 'Gagal',
                        text: 'Update Data Gagal!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
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
                        text: 'Update Data Gagal!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);
                });
        }
    };
function findWithAttr(array, attr, value) {
        for (var i = 0; i < array.length; i += 1) {
            if (array[i][attr] === value) {
                return i;
            }
        }
    }
}]);