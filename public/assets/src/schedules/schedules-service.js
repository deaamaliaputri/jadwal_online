/**
 * Created by - LENOVO - on 24/08/2015.
 */
app.factory('schedules', ['$http', function ($http) {
    return {
        // get data dengan pagination dan pencarian data
        get: function (page, term) {
            return $http({
                method: 'get',
                url: '/api/schedules?page=' + page + '&term=' + term,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }
            });
        },

        getListsubjects: function () {
            return $http({
                method: 'get',
                url: '/api/getList-subjects',
            });
        },

        getListteachers: function () {
            return $http({
                method: 'get',
                url: '/api/getList-teachers',
            });
        },

        getListteacherss: function () {
            return $http({
                method: 'get',
                url: '/api/getList-teachers',
            });
        },

        

        getListdepartment: function () {
            return $http({
                method: 'get',
                url: '/api/getList-departments',
            });
        },

        getListkelas: function () {
            return $http({
                method: 'get',
                url: '/api/getList-kelas',
            });
        },

        //Simpan data
        store: function (inputData) {
            return $http({
                method: 'POST',
                url: '/api/schedules',
                data: $.param(inputData)
            });
        },

        //Tampil Data
        show: function (_id) {
            return $http({
                method: 'get',
                url: '/api/schedules/' + _id,
            });
        },

        destroy: function (_id) {
            return $http({
                method: 'delete',
                url: '/api/schedules/' + _id,
            });
        },

        //Update data
        update: function (inputData) {
            return $http({
                method: 'put',
                url: '/api/schedules/' + inputData.id,
                data: $.param(inputData)
            });
        },
        cekcetak: function (_id,_id2) {
            return $http({
                method: 'get',
                url: '/api/cek-cetak-schedules/' + _id +'/'+ _id2,
            });
        },

    }
}]);