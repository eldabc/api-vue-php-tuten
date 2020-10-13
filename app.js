var app = new Vue({
  el: '#vueapp',
  data: {
	hour: '',
	utc: '',
	message: '',
	regs: []
  	},
  methods: {

	reloadList: function() {
 		this.$http.get('/api-vue-php-tuten/src/times.php').then(function(response){
        		this.regs = response.body;
        		this.message='';
      		}, function(){
	        	alert('Error! al traer los datos.');
      		});
		},

	enviar: function() {
		this.$http.post('/api-vue-php-tuten/src/times.php',{ 
			hour: this.hour, 
			utc: this.utc, 
			}).then(function(response){
				console.log(response.body)
        		this.regs = response.body;
				this.hour="";
				this.utc="";
      		});
		}

	},
  mounted () {
	this.reloadList();
  }
});


