class BX24Wrapper {
  constructor() {

    if (!window.BX24) {
      throw `Can't find BX24 libary! See https://dev.1c-bitrix.ru/rest_help/js_library/index.php`;
    }

    this.throttle = 2;
    this.lastRequestTime = 0;
    this.lastResult = {};
  }

  async call(method, params) {
    await this.throttleCall();
    return new Promise((resolve, reject) => {
      BX24.callMethod(method, params, (result) => {
        this.lastResult = result;
        if (result.error()) {
          return reject({
            error: result.error(),
            method: method,
            params: params
          });
        }
        resolve(result);
      });
    });
  }
  
  async callBatch(calls, bHaltOnError = false) {
    await this.throttleCall();
    return new Promise((resolve, reject) => {
      BX24.callBatch(calls, (result) => {
        this.lastResult = result;
        resolve(result);
      }, bHaltOnError);
    });
  }

  async next(result = this.lastResult) {
    if (!result.more()) {
      return null;
    }
    await this.throttleCall();
    return new Promise((resolve, reject) => {
      result.next((result) => {
        this.lastResult = result;
        if (result.error()) {
          return reject({
            error: result.error(),
            method: method,
            params: params
          });
        }
        resolve(result);
      });
    });
  }

  throttleCall() {
    return new Promise(resolve => {
      let timeout = Math.round(this.lastRequestTime + 1e3 * (1 / this.throttle) - Date.now());
      if (timeout <= 0) {
        this.lastRequestTime = Date.now();
        return resolve();
      }
      setTimeout(() => {
        this.lastRequestTime = Date.now();
        return resolve();
      }, timeout);
    });
  }

}
