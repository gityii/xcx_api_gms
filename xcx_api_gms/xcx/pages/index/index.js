//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    motto: 'Hello World',
    userInfo: {},
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo')
  },

  //读取数据
  bindReadTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles',
      header: {
        'Content-Type': 'application/json'
      },
      method: 'GET',
      data:{
        page:1  //此处翻页
      },
      success: function (res) {
        page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },
  
  //新增数据
  bindCreateTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles',
      header: {
        'Content-Type': 'application/json'
      },
      method: 'POST',
      data:{
        title:"hello game",
        content:"youngest time gogogo",
        tags:"Yii2",
        status:3,
        author_id:1
      },
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  }, 
  
  //修改数据
  bindUpdateTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles/43',
      header: {
        'Content-Type': 'application/json'
      },
      method: 'PUT',
      data: {
        title: "hello game modify",
        content: "youngest time gogogo",
        tags: "Yii2",
        status: 3,
        author_id: 1
      },
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },

  //删除数据
  bindDeleteTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles/43',
      header: {
        'Content-Type': 'application/json'
      },
      method: 'DELETE',
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },



  //其他http动词
  bindOtherTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles',
      header: {
        'Content-Type': 'application/json'
      },
      method: 'OPTIONS',
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },



  //文章搜索
  bindSearchTap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/articles/search',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      data:{
        keyword:'详解'//通过关键字搜索，搜索title里面含有 详解 的文章
      },
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },



  //TOP10
  bindTop10Tap: function () {
    var page = this;
    wx.request({
      url: 'http://xcx.com/top10',
      header: {
        'Content-Type': 'application/json'
      },
      success: function (res) {
        //page.setData({ motto: res.data.title })
        console.log(res.data)
      }
    })
  },


  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad: function () {
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
  },
  getUserInfo: function(e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
    })
  }
})
