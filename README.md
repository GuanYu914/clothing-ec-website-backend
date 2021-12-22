# APIs 相關資訊
此 APIs 適用於此 [clothing-ec](https://github.com/GuanYu914/clothing-ec-website) 專案，下方是每個 API 的格式訊息
## handleGetBanners.php
**呼叫時機** : 拿首頁的 Banner 資訊  
**要求方法** : GET  
**傳入參數** : 不用  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  data        : 2d ARRAY, banners info            // Banners 資訊
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleGetCartItem.php
**呼叫時機** : 拿用戶的購物車資訊  
**要求方法** : GET  
**傳入參數** : SESSION COOKIE  
**回傳格式** : encoded JSON string     
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'      // request 是否成功
  data        : 2d ARRAY, cart items of current user  // 要求資料
  msg         : STRING, 'message'                     // 回應訊息
  detail      : STRING, 'detail message for msg'      // 附加回應訊息
}
```

## handleGetCategories.php
**呼叫時機** : 拿首頁或所有產品頁面的分類資訊  
**要求方法** : GET  
**傳入參數** : QueryString  
**使用說明** : handleGetCategories.php?type={ 參數 }  
**參數說明** : 傳入 main，回傳主要分類，傳入 detail，回傳詳細分類  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'          // request 是否成功
  data        : 2d ARRAY, categories data based on query  // 根據 query string 回傳的分類資訊
  msg         : STRING, 'message'                         // 回應訊息
  detail      : STRING, 'detail message for msg'          // 附加回應訊息
}
```

## handleGetFavoriteItems.php
**呼叫時機** : 拿用戶的收藏清單  
**要求方法** : GET  
**傳入參數** : SESSION COOKIE  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'        // request 是否成功
  data        : ARRAY, favorite items of current user   // 用戶收藏清單
  msg         : STRING, 'message'                       // 回應訊息
  detail      : STRING, 'detail message for msg'        // 附加回應訊息
}
```

## handleGetHotItems.php
**呼叫時機** : 拿首頁的熱銷產品  
**要求方法** : GET  
**傳入參數** : QueryString  
**使用說明** : handleGetHotItems.php?limit={ 參數1 }&&offset={ 參數2 }  
**參數說明** : 參數1為限制回傳資料總筆數，參數2則是跳過多少筆資料  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  totals      : NUMBER, total data counts         // 資料總比數 ( 指的是資料庫內所有筆數，不是當前回傳的筆數 )
  data        : 2d ARRAY, hot items               // 熱銷產品
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleGetProduct.php
**呼叫時機** : 拿產品所有資訊  
**要求方法** : GET  
**傳入參數** : QueryString  
**使用說明** : handleGetProduct.php?id={ 參數 }  
**參數說明** : 參數為產品的 pid  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  data        : 2d ARRAY, product info of pid     // 產品編號的詳細資訊
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleGetProducts.php
**呼叫時機** : 根據分類回傳相對應的產品  
**要求方法** : GET  
**傳入參數** : QueryString  
**使用說明** : handleGetProducts.php?main={ 參數1 }&&sub={ 參數2 }&&detailed={ 參數3 }&&limit={ 參數4 }&&offset={ 參數5 }  
**參數說明** : 
參數 1 : 主要分類  
參數 2 : 子分類  
參數 3 : 詳細分類  
參數 4 : 限制回傳資料總筆數  
參數 5 : 跳過多少筆資料  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING,  'failed' or 'successful'           // request 是否成功
  totals      : NUMBER, totals data count                   // 資料總比數 ( 指的是資料庫內所有筆數，不是當前回傳的筆數 )
  data        : ARRAY, matched products of query string     // 符合分類的所有產品
  msg         : STRING, 'message'                           // 回應訊息
  detail      : STRING, 'detail message for msg'            // 附加回應訊息
} 
```

## handleGetSession.php
**呼叫時機** : 拿用戶身分資訊  
**要求方法** : GET  
**傳入參數** : SESSION COOKIE  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  data        : ARRAY, current user info          // 目前用戶資訊 
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleGetUserComments.php
**呼叫時機** : 拿到首頁的用戶評價資訊  
**要求方法** : GET  
**傳入參數** : QueryString  
**使用說明** : handleGetUserComments.php?limit={ 參數1 }&&offset={ 參數2 }  
**參數說明** : 參數1為限制回傳資料總筆數，參數2則是跳過多少筆資料  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  data        : 2d ARRAY, user comments           // 用戶評論
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleLogin.php
**呼叫時機** : 用戶登入，並建立 SESSION  
**要求方法** : POST  
**傳入欄位** : account, password  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleLogout.php
**呼叫時機** : 用戶登出，刪除 SESSION  
**要求方法** : GET  
**傳入參數** : 不用  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleProfileEdit.php
**呼叫時機** : 用戶試著更改用戶資訊  
**要求方法** : POST  
**傳入欄位** : account ( SESSION COOKIE ), nickname, password  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleRegister.php  
**呼叫時機** : 訪客發送註冊資訊，並建立 SESSION  
**要求方法** : POST  
**傳入資料** : nickname, account, password  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleUploadCartItems.php  
**呼叫時機** : 上傳當前用戶的購物車資料  
**要求方法** : POST  
**傳入資料** : 購物車物品 json 資料  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```

## handleUploadFavoriteItems.php  
**呼叫時機** : 上傳當前用戶的收藏清單  
**要求方法** : POST  
**傳入資料** : 收藏清單 json 資料  
**回傳格式** : encoded JSON string    
```js
// 回傳資料
{
  isSuccessful: STRING, 'failed' or 'successful'  // request 是否成功
  msg         : STRING, 'message'                 // 回應訊息
  detail      : STRING, 'detail message for msg'  // 附加回應訊息
}
```


