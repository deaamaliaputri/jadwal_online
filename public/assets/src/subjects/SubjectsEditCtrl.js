app.controller('SubjectsEditCtrl', ['$state', '$scope', 'subjects', 'SweetAlert', 'toaster', '$stateParams', function ($state, $scope, subjects, SweetAlert, toaster, mdToast, $stateParams) {
    $scope.id = $scope.$stateParams.id;
    //edit subjects
    //If Id is empty, then redirected
    if ($scope.id == null || $scope.id == '') {
        $state.go("app.subjects")
    }
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
    //get lass subjects
$scope.objTeachers =[]
subjects.getListteachers()
        .success(function (data_akun) {
            if (data_akun.success == false) {
                        $scope.toaster = {
                        type: 'warning',
                        title: 'Warning',
                        text: 'Data Belum Tersedia!'
                    };
                    toaster.pop($scope.toaster.type, $scope.toaster.title, $scope.toaster.text);

            } else {
                data_akun.unshift({id: 0, name: 'Silahkan Pilih Guru'});
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



    //Run Ajax
    subjects.show($scope.id)
        .success(function (data) {
            $scope.setLoader(false);
            $scope.myModel = data;
            subjects.getListteachers()
                .success(function (datajk) {
                    datajk.unshift({id: 0, name: 'Silahkan pilih Guru'});
                    $scope.objTeachers = datajk;
                    $scope.myModel.teachers = $scope.objTeachers[0];
                    $scope.myModel.teachers = $scope.objTeachers[findWithAttr($scope.objTeachers, 'id', parseInt(data.teachers_id))];
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
            subjects.update($scope.myModel)
                .success(function (data) {
                    if (data.updated == true) {
                        //If back to list after submitting
                        //Redirect to akun
                        $state.go('app.subjects');
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