export const LoggerMixin = {
  log(message) {
    console.log(`[${this.constructor.name}]: ${message}`);
  },
}
