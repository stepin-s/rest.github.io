function Order(config){
  this.config = config;
  return this;
}

Order.prototype = Object.create(OrderCommon.prototype);
Order.prototype.constructor = Order;