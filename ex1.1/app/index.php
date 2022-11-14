<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>app</title>
  <script src="//api.bitrix24.com/api/v1/"></script>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
</head>

<body>
  <button id="test">test</button>
  <pre id="status"></pre>
  <script>
    const TIMEOUT = 500;
    const BX24Client = {
      call: function (method, params) {
        return new Promise((resolve, reject) => {
          setTimeout(function () {
            BX24.callMethod(method, params, (result) => {
              if (result.error()) {
                return reject({
                  error: result.error(),
                  method: method,
                  params: params
                });
              }
              resolve(result);
            });
          }, TIMEOUT);
        });
      },
      callBatch: function (calls, bHaltOnError = false) {
        return new Promise((resolve, reject) => {
          setTimeout(function () {
            BX24.callBatch(calls, (result) => {
              resolve(result);
            }, bHaltOnError);
          }, TIMEOUT);
        });
      },
      next: function (result) {
        if (!result.more()) {
          return null;
        }
        return new Promise((resolve, reject) => {
          setTimeout(function () {
            result.next((result) => {
              if (result.error()) {
                return reject({
                  error: result.error(),
                  method: method,
                  params: params
                });
              }
              resolve(result);
            });
          }, TIMEOUT);
        });
      }
    };

    const tasks = [51144, 51146, 51148];
    const comments = ['рандомный', 'комментарий', 'к списанному', 'времени'];

    const getRandomItem = function (arr) {
      return arr[Math.floor(Math.random() * arr.length)];
    };

    const getRandomInt = function (min, max) {
      return Math.floor(Math.random() * (max - min + 1) + min)
    }

    const date2str = function (d) {
      return d.getFullYear() + '-' + paddatepart(1 + d.getMonth()) + '-' + paddatepart(d.getDate()) + 'T' + paddatepart(d.getHours())
        + ':' + paddatepart(d.getMinutes()) + ':' + paddatepart(d.getSeconds()) + '+03:00';
    };

    const paddatepart = function (part) {
      return part >= 10 ? part.toString() : '0' + part.toString();
    };

    $(document).ready(function () {
      $('#test').click(async function () {
        let res = await BX24Client.call('user.get', {});
        const users = res.data();
        for (let user of users) {
          console.log('=============================');
          let now = new Date();
          for (let date = new Date(now.getFullYear(), now.getMonth(), 1); date <= now; date.setDate(date.getDate() + 1)) {
            if (date.getDay() !== 0 && date.getDay() !== 6) {
              let res = await BX24Client.call(
                'task.elapseditem.add',
                [getRandomItem(tasks), { SECONDS: getRandomInt(1, 5) * 60 * 60, COMMENT_TEXT: getRandomItem(comments), CREATED_DATE: date2str(date), USER_ID: parseInt(user.ID) }],
              );
              console.log(res);
            }
          }
          console.log('=============================');
        }
      });
    });

    BX24.init(app);
    function app() {
      const initDate = BX24.getAuth();
    }
    BX24.fitWindow();
  </script>
</body>

</html>