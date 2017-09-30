
angular.module('Todo', []).factory('myhttpserv', function ($http) {
    return $http.get('mailling-list.txt').error(function(status){console.log(status)});
}).controller('TodoController', function ($scope, myhttpserv, $http) {
    $scope.appTitle = "MyTodoList",
    myhttpserv.then(function(response){
        $scope.todos = (response.data !== null) ? response.data : [];
        var httpPost = function() {
            $http.post('mailling-list.php', JSON.stringify($scope.todos)).error(function(status){console.log(status)});
        };
		
			$scope.addTodo = function() {
			$msgbox.show("Ok clicked", {title: "Success"});
            $scope.todos.push({
                text: $scope.todoText,
                doneProd: false,
                doneDev: false
            });
            $scope.todoText = ''; //clear the input after adding
            httpPost();
        };

        $('.splash, .container').fadeToggle();
    });
});