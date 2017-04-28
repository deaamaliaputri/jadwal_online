/**
 * Created by - LENOVO - on 24/08/2015.
 */
app.factory('students', ['$http', function ($http) {
    return {
        // get data dengan pagination dan pencarian data
        get: function (page, term) {
            return $http({
                method: 'get',
                url: '/api/students?page=' + page + '&term=' + term,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}
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
                url: '/api/students',
                data: $.param(inputData)
            });
        },

        //Tampil Data
        show: function (_id) {
            return $http({
                method: 'get',
                url: '/api/students/' + _id,
            });
        },

        destroy: function (_id) {
            return $http({
                method: 'delete',
                url: '/api/students/' + _id,
            });
        },

        //Update data
        update: function (inputData) {
            return $http({
                method: 'put',
                url: '/api/students/' + inputData.id,
                data: $.param(inputData)
            });
        },

    }
}]);