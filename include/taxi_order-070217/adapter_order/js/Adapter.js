function Adapter(config){
  this.config = config;
  return this;
}

Adapter.prototype = Object.create(AdapterOrder.prototype);
Adapter.prototype.constructor = Adapter;