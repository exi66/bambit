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
  /**
   * Функция, реализующая алгоритм https://dev.1c-bitrix.ru/rest_help/rest_sum/start.php 
   * и функционал макросов Batch'a https://dev.1c-bitrix.ru/rest_help/js_library/rest/callBatch.php
   * Принимает на вход объект settings, который должен содержать описание листового метода
   * Пример:
   * {
   *  method: 'tasks.task.list',
   *  resWrapper: 'tasks', //обертка ответа 
   *  //например ответ задач при выполенении .data() вернет объект { tasks: [...] }, в данном случае tasks и есть враппер
   *  //если отсутсвует, то указывать не нужно
   *  idWrapper: 'id', //обертка поля id в ответе
   *  //аналогичный пример на задачах: { tasks: [ { id: 12021... }, ... ] }, id и есть враппер в данном случае. 
   *  //У лидов, например, это будет ID
   *  //по умолчанию ID
   *  filter: ...,
   *  select: ...,
   * }
   * 
   * @param {Object} settings 
   * @param {boolean} bHaltOnError 
   * @returns 
   */
  async callList(settings, bHaltOnError = false) {
    const __method = settings.method,
      __wrapper = settings.resWrapper || null,
      __id_wrapper = settings.idWrapper || 'ID',
      __filter = settings.filter,
      __select = settings.select,
      __order = { 'ID': 'ASC' };
    if (!__method || !__id_wrapper || !__filter || !__select) return;
    if (!__select.includes('ID')) __select.push('ID');
    let count = await this.call(__method, { order: __order, filter: __filter, select: [__id_wrapper] }).total(),
      lastResID = __filter['>ID'] || 0,
      resultData = [];
    while (count > 0) {
      let localFilter = JSON.parse(JSON.stringify(__filter));
      localFilter['>ID'] = lastResID;
      let localCount = Math.min(Math.ceil(count / 50), 50),
        localCalls = [{
          method: __method,
          params: {
            order: __order,
            filter: localFilter,
            select: __select,
            start: -1,
          }
        }];
      for (let i = 1; i < localCount; i++) {
        let localFilter = JSON.parse(JSON.stringify(__filter));
        localFilter['>ID'] = `$result[${i - 1}]${__wrapper ? '[' + __wrapper + ']' : ''}[49][${__id_wrapper}]`;
        localCalls[i] = {
          method: __method,
          params: {
            order: __order,
            filter: localFilter,
            select: __select,
            start: -1,
          }
        }
      }
      let resList = await this.callBatch(localCalls, bHaltOnError);
      for (let res of resList) {
        resultData = resultData.concat(__wrapper ? res.data()[__wrapper] : res.data());
      }
      lastResID = resultData[resultData.length - 1][__id_wrapper];
      count -= 2500;
    }
    return resultData;
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
