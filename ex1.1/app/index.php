<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>app</title>
  <script src="//api.bitrix24.com/api/v1/"></script>
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"
    integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
    integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
  <div class="p-4">
    <div>
      Начальная дата: <input id="start_date" type="date" class="btn btn-light border"> 
      Конечная: <input id="end_date" type="date" class="btn btn-light border">
      <button id="test" type="button" class="btn btn-primary">Запустить</button>
    </div>
    <div class="mt-2">
      <span id="status" class="badge text-bg-secondary">Пользователь 0/0</span>
      <span id="status2" class="badge text-bg-secondary">Запрос 0/0</span>
    </div>
  </div>
  <script>
    const tasks = [51144, 51146, 51148];
    const comments = ['рандомный', 'комментарий', 'к списанному', 'времени'];
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

      //костыль разных таймзон сервера и клиента
      let now = new Date(
        new Date().toLocaleString('en-US', { timeZone: 'Europe/Moscow' })
      );

      $('#start_date').attr('max', `${now.getFullYear()}-${('0' + (now.getMonth() + 1)).slice(-2)}-${('0' + now.getDate()).slice(-2)}`);
      $('#start_date').val(`${now.getFullYear()}-${('0' + (now.getMonth() + 1)).slice(-2)}-01`);
      $('#end_date').attr('max', `${now.getFullYear()}-${('0' + (now.getMonth() + 1)).slice(-2)}-${('0' + now.getDate()).slice(-2)}`);
      $('#end_date').val(`${now.getFullYear()}-${('0' + (now.getMonth() + 1)).slice(-2)}-${('0' + now.getDate()).slice(-2)}`);

      $('#test').click(async function () {

        let end_date = new Date($('#end_date').val()), start_date = new Date($('#start_date').val());
        if (start_date.getTime() > end_date.getTime()) return console.error('Start date cannot be > end date!');

        let res = await BX24Client.call('user.get', {});
        const users = res.data();

        let users_count = 1;

        for (let user of users) {
          /* 
          *  Минимальная визуализация прогресса выполнения
          */
          $('#status').text(`Пользователь: ${users_count}/${users.length}`);
          console.log(`==== USER #${users_count++} =====`);
          let actions_count = 0;

          for (let date = new Date(start_date.getTime()); date <= end_date; date.setDate(date.getDate() + 1)) {
            if (date.getDay() !== 0 && date.getDay() !== 6) {
              /* 
              *  Минимальная визуализация прогресса выполнения
              */
              $('#status2').text(`Запрос: ${actions_count}`);
              console.log(`Запрос #${actions_count++}:`);

              let res = await BX24Client.call(
                'task.elapseditem.add',
                [getRandomItem(tasks), { SECONDS: getRandomInt(1, 5) * 60 * 60, COMMENT_TEXT: getRandomItem(comments), CREATED_DATE: date2str(date), USER_ID: parseInt(user.ID) }],
              );
              console.log(res);
            }
          }
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