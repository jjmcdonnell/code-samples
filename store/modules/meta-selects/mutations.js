let mutations = {
  UPDATE_META_LIST(state, response, listName){
    switch(listName){
      case 'ArtThemes':{
        state.ArtThemes = response;
      }break;
      case 'Brands':{
        state.Brands = response;
      }break;
      case 'Categories':{
        state.Categories = response;
      }break;
      case 'Characters':{
        state.Characters = response;
      }break;
      case 'Conditions':{
        state.Conditions = response;
      }break;
      case 'Cultures':{
        state.Cultures = response;
      }break;
      case 'DecorStyles':{
        state.DecorStyles = response;
      }break;
      case 'Eras':{
        state.Eras = response;
      }break;
      case 'Franchises':{
        state.Franchises = response;
      }break;
      case 'Materials':{
        state.Materials = response;
      }break;
      case 'Occasions':{
        state.Occasions = response;
      }break;
      case 'Shapes':{
        state.Shapes = response;
      }break;
      case 'ManufacturerIdTypes':{
        state.ManufacturerIdTypes = response;
      }break;
    }
  }
}
export default mutations;