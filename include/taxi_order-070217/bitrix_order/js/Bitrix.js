function Bitrix(config){
  this.config = config;
  return this;
}

Bitrix.prototype = Object.create(BitrixOrder.prototype);
Bitrix.prototype.constructor = Bitrix;